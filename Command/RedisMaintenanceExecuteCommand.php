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
            ->setName('rednose:framework:redis-maintenance:execute')
            ->setDescription('Executes redis maintenance classes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer();
        $kernelPath = realpath($container->getParameter('kernel.root_dir') . '/../');

        $kernelPath         = realpath($container->getParameter('kernel.root_dir') . '/../');
        $maintenanceService = $container->get('rednose_framework.redis.maintenance');
        $maintenancePath    = $kernelPath . '/' . $container->getParameter('rednose_framework.redis.maintenance_path');
        $maintenanceFiles   = glob($maintenancePath . '/Version*.php');

        $count = $maintenanceService->process($maintenanceFiles);

        $output->writeln($count . ' maintenance classes executed.');
    }
}
