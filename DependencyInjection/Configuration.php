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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for this bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('rednose_framework', 'array');

        $this->addUserSection($rootNode);
        $this->addRedisSection($rootNode);
        $this->addAclSection($rootNode);
        $this->addAccountSection($rootNode);
        $this->addSamlSection($rootNode);

        return $treeBuilder;
    }

    private function addUserSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('user')->defaultTrue()->end()
            ->end();
    }

    private function addRedisSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('redis')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('maintenance_path')->defaultValue('app/redis_maintenance')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addAclSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('acl')->defaultFalse()->end()
            ->end();
    }

    private function addAccountSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('auto_account_creation')->defaultFalse()->end()
            ->end();
    }

    private function addSamlSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('saml')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')->defaultFalse()->end()
                            ->scalarNode('username_attr')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
