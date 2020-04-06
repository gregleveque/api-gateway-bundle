<?php

namespace Gie\GatewayBundle\DependencyInjection\Compiler;

use Gie\Gateway\API\Cache\Adapter\SetAdapterInterface;
use Gie\Gateway\Core\Cache\Adapter\TraceableSetAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CacheCollectorPass implements CompilerPassInterface
{

    private const REDIS_SET_ADAPTER_ID = 'gie_gateway.cache.redis';
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('data_collector.cache')) {
            return;
        }

        $definition = $container->getDefinition(self::REDIS_SET_ADAPTER_ID);
        $definition->setClass(TraceableSetAdapter::class);

        $container->setDefinition(self::REDIS_SET_ADAPTER_ID, $definition);
    }
}
