<?php

namespace Openpp\TimelineBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('openpp_timeline');

        $supportedManagerTypes = array('orm');

        $rootNode
            ->children()
                ->scalarNode('drivers')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedManagerTypes)
                        ->thenInvalid('The db_driver %s is not supported. Please choose one of ' . json_encode($supportedManagerTypes))
                    ->end()
                ->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('timeline')->defaultValue('Application\\Openpp\\TimelineBundle\\Entity\\Timeline')->end()
                        ->scalarNode('action')->defaultValue('Application\\Openpp\\TimelineBundle\\Entity\\Action')->end()
                        ->scalarNode('component')->defaultValue('Application\\Openpp\\TimelineBundle\\Entity\\Component')->end()
                        ->scalarNode('action_component')->defaultValue('Application\\Openpp\\TimelineBundle\\Entity\\ActionComponent')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
