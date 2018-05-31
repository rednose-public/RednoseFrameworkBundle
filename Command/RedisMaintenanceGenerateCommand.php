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
            ->setName('rednose:framework:redis-maintenance:generate')
            ->setDescription('Command generates a blank maintenance class');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer();
        $kernelPath = realpath($container->getParameter('kernel.root_dir') . '/../');

        $maintenancePath = $kernelPath . '/' . $container->getParameter('rednose_framework.redis.maintenance_path');
        $generatedName   = 'Version' . date('YmdHis');

        $classTemplate = '
<?php

namespace Rednose\FrameworkBundle\Redis;

class ' . $generatedName . '
{
    /**
     * @var RedisFactory $factory 
     */
    public function up(RedisFactory $factory)
    {
        $redis = $factory->getClient();
        
        if ($redis !== null) {
            /** Your implementation here */
        }
    }

    /**
     * @return bool 
     */
    public function runOnce()
    {
        return true;
    }
}';

        if (is_writable($maintenancePath) === false) {
            throw new \Exception('Unable to write file ' . $maintenancePath . ' does not exist');
        }

        $filename = $maintenancePath . '/' . $generatedName . '.php';

        file_put_contents($filename, trim($classTemplate));

        $output->writeln('Generated new maintenance class to "<info>' . $filename . '</info>"');
    }
}
