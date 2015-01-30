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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

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
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['oauth']) {
            $loader->load('oauth.xml');
        }

        if ($config['acl']) {
            $loader->load('acl.xml');
        }

        $serviceFiles = array('orm', 'services', 'twig');

        foreach ($serviceFiles as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if ($config['user']) {
            $loader->load('admin.xml');

            $this->loadAccount($config['auto_account_creation'], $container);
        }

        $this->setSAMLParameter($container, $config);
    }

    private function loadAccount($config, ContainerBuilder $container)
    {
        $container->getDefinition('rednose_framework.user_manager')->replaceArgument(5, $config);
    }

    private function setSAMLParameter(ContainerBuilder $container, $config)
    {
        $container->setParameter('rednose_framework.use_saml', $config['use_saml']);
    }
}
