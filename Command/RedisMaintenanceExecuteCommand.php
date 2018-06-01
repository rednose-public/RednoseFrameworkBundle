<?php

namespace Rednose\FrameworkBundle\Command;

use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisMaintenanceExecuteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rednose:framework:redis-execute')
            ->setDescription('Executes redis maintenance classes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer();
        $kernelPath = realpath($container->getParameter('kernel.root_dir') . '/../');

        $maintenancePath = $container->getParameter('rednose_framework.redis.maintenance_path');

        if ($maintenancePath[0] !== '/') {
            $maintenancePath = $kernelPath . '/' . $container->getParameter('rednose_framework.redis.maintenance_path');
        }

        $maintenanceService = $container->get('rednose_framework.redis.maintenance');
        $maintenanceFiles   = glob($maintenancePath . '/Task*.php');

        $count = $maintenanceService->process($maintenanceFiles);

        $output->writeln('<info>' . $count . '</info> redis maintenance tasks executed.');
    }
}
