<?php

namespace Rednose\FrameworkBundle\Command;

use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisMaintenanceGenerateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rednose:framework:redis-generate')
            ->setDescription('Command generates a blank maintenance task class');
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

        $generatedName   = 'Version' . date('YmdHis');

        $classTemplate = file_get_contents(__DIR__ . '/../DataFixtures/Redis/RedisMaintenanceTaskOnce.phps');
        $classTemplate = str_replace(' RedisMaintenanceTaskOnce ', ' ' . $generatedName . ' ', $classTemplate);
        $classTemplate = str_replace('Rednose\FrameworkBundle\DataFixtures\Redis', 'Rednose\FrameworkBundle\Redis\Maintenance', $classTemplate);

        if (is_writable($maintenancePath) === false) {
            throw new \Exception('Unable to write file ' . $maintenancePath . ' does not exist');
        }

        $filename = $maintenancePath . '/' . $generatedName . '.php';

        file_put_contents($filename, trim($classTemplate));

        $output->writeln('Generated new maintenance class to "<info>' . $filename . '</info>"');
    }
}
