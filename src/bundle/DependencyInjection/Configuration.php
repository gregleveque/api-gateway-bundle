<?php

namespace Gie\GatewayBundle\DependencyInjection;

use Gie\Gateway\Core\Cache\CacheManager;
use Gie\GatewayBundle\Event\Events;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('gie_gateway');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('redis_dsn')
                    ->info('Host of Redis instance.')
                ->defaultValue('127.0.0.1')
                ->end()
                ->arrayNode('routes')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('target')->end()
                            ->arrayNode('query')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode('headers')
                                ->useAttributeAsKey('name')
                                ->scalarPrototype()->end()
                            ->end()
                            ->integerNode('ttl')
                                ->defaultValue(CacheManager::DEFAULT_TTL)
                            ->end()
                            ->enumNode('aggregator')
                                ->defaultValue('array')
                                ->values(['array', 'duplicate', 'comma'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }


}
