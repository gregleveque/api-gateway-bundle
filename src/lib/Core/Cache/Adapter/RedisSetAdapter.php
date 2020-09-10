<?php

namespace Gie\Gateway\Core\Cache\Adapter;

use Gie\Gateway\API\Cache\Adapter\SetAdapterInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Traits\RedisTrait;

class RedisSetAdapter extends AbstractAdapter implements SetAdapterInterface
{
    use RedisTrait;

    /** @var int  */
    private const POP_MAX_LIMIT = 2147483647 - 1;

    /** @var string  */
    protected $namespace;
    /**
     * RedisSetAdapter constructor.
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redisClient
     * @param string $namespace
     * @param int $defaultLifetime
     */
    public function __construct($redisClient, $namespace = '', $defaultLifetime = 0)
    {
        $this->init($redisClient, $namespace, $defaultLifetime,null);
        $this->namespace = $namespace;
    }

    /**
     * @inheritDoc
     */
    public function addItemsInSet(string $key, ...$members): int
    {
        return $this->redis->sAdd($this->namespace . parent::NS_SEPARATOR . $key, ...$members);
    }

    /**
     * @inheritDoc
     */
    public function listAllItemsInSet(string $key): array
    {
        return $this->redis->sMembers($this->namespace . parent::NS_SEPARATOR . $key);
    }

    /**
     * @inheritDoc
     */
    public function getAllItemsInSet(string $key): array
    {
        return $this->redis->sPop($this->namespace . parent::NS_SEPARATOR . $key, self::POP_MAX_LIMIT);
    }

}