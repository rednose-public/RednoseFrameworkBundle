<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads the configuration files and injects them to the service container.
 */
class RednoseFrameworkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));


        $bundles = $container->getParameter('kernel.bundles');

        if ($config['acl']) {
            $loader->load('acl.xml');
        }

        $serviceFiles = ['assigner', 'orm', 'services', 'twig', 'session'];

        // Only load the redis session service if it is configured
        try {
            $redisConfig = $container->getParameter('redis_host');
        } catch (\Exception $e) {
            $container->setParameter('redis_host', '');
            $container->setParameter('redis_auth', '');
            $container->setParameter('redis_session_expire', 0);
        }

        foreach ($serviceFiles as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if ($config['user']) {
            $loader->load('admin.xml');

            $this->loadAccount($config, $container);
        }

        if (isset($bundles['AerialShipSamlSPBundle'])) {
            $loader->load('saml.xml');

            if ($config['saml']) {
                $this->setSAMLParameter($container, $config);
            }
        }
    }

    private function loadAccount($config, ContainerBuilder $container)
    {
        $container->getDefinition('rednose_framework.user_manager')->replaceArgument(6, $config['auto_account_creation']);
    }

    private function setSAMLParameter(ContainerBuilder $container, $config)
    {
        $container->getDefinition('rednose_framework.user_manager')->replaceArgument(6, $config['auto_account_creation']);

        if (isset($config['saml']['username_attr'])) {
            $container->getDefinition('rednose_framework.user_manager')->replaceArgument(7, $config['saml']['username_attr']);
        }
    }
}
