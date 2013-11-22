<?php

namespace Kw\ParserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kw_parser');
        $rootNode
            ->children()
            ->arrayNode('cfg')
            ->isRequired()
            ->children()
            ->scalarNode('start')->defaultNull()->end()
            ->arrayNode('productions')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('array')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('terminals')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
