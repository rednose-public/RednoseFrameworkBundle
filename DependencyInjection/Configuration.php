<?php

namespace Rednose\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Rednose\FrameworkBundle\Form\Type\EditorType;

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
        $this->addOauthSection($rootNode);
        $this->addAclSection($rootNode);
        $this->addFormSection($rootNode);

        return $treeBuilder;
    }

    private function addUserSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('user')->defaultTrue()->end()
            ->end();
    }

    private function addOauthSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('oauth')->defaultFalse()->end()
            ->end();
    }

    private function addAclSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('acl')->defaultFalse()->end()
            ->end();
    }

    private function addFormSection(ArrayNodeDefinition $node)
    {
        $supportedEditors = array(EditorType::TYPE_TINYMCE, EditorType::TYPE_CKEDITOR);

        $node
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('editor')
                            ->isRequired()
                            ->defaultValue(EditorType::TYPE_TINYMCE)
                            ->validate()
                                ->ifNotInArray($supportedEditors)
                                ->thenInvalid('The editor %s is not supported. Please choose one of '.json_encode($supportedEditors))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
