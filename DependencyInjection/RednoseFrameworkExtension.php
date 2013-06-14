<?php

namespace Rednose\FrameworkBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Loads the configuration files and injects them to the service container.
 *
 * @author Marc Bontje <marc@rednose.nl>
 */
class RednoseFrameworkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $serviceFiles = array('admin', 'orm', 'services');

        foreach ($serviceFiles as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
    }
}
