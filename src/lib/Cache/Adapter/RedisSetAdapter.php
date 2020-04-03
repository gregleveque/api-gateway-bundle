<?php

namespace Gie\Gateway\Cache\Adapter;



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
        $this->init($redisClient, $namespace, $defaultLifetime);
        $this->namespace = $namespace;
    }

    /**
     * @inheritDoc
     */
    public function addItemsInSet(string $key, ...$members) {
        return $this->redis->sAdd($this->namespace . parent::NS_SEPARATOR . $key, ...$members);
    }

    /**
     * @inheritDoc
     */
    public function popRandomItemInSet(string $key)
    {
        return $this->redis->sPop($this->namespace . parent::NS_SEPARATOR . $key);
    }

    /**
     * @inheritDoc
     */
    public function getAllItemsInSet(string $key)
    {
        return $this->redis->sPop($this->namespace . parent::NS_SEPARATOR . $key, self::POP_MAX_LIMIT);
    }

}