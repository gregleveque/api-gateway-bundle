<?php

namespace Gie\Gateway\Cache;

use Gie\Gateway\Cache\Adapter\SetAdapterInterface;
use Gie\Gateway\Request\DeferredRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Adapter\AdapterInterface;


class CacheManager
{
    /** @var int  */
    private const DEFAULT_TTL = 60;

    private const QUEUE_SET_KEY = 'deferred-requests';

    /** @var SetAdapterInterface  */
    protected $cache;

    /** @var int  */
    protected $default_ttl;

    public function __construct(SetAdapterInterface $cache, int $default_ttl = self::DEFAULT_TTL)
    {
        $this->cache = $cache;
        $this->default_ttl = $default_ttl;
    }

    /**
     * @param string $key
     * @param Response $response
     * @param null $ttl
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function saveResponse(string $key, Response $response, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = self::DEFAULT_TTL;
        }

        $item = $this->cache->getItem($key)
            ->set($response)
            ->expiresAfter($ttl);

        return $this->cache->save($item);
    }

    /**
     * @param string $key
     * @return bool|Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getResponse(string $key)
    {
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            return false;
        }
    }

    /**
     * @param DeferredRequest $request
     * @return bool|false|int|mixed
     */
    public function deferRequest(DeferredRequest $request)
    {
        return $this->cache->addItemsInSet(self::QUEUE_SET_KEY, \serialize($request));
    }

    /**
     * @return array
     */
    public function getDeferredRequests()
    {
        return array_map(function ($request) {
            return \unserialize($request);
        }, $this->cache->getAllItemsInSet(self::QUEUE_SET_KEY));
    }

    public function listDeferredRequests()
    {
        return array_map(function ($request) {
            return \unserialize($request);
        }, $this->cache->listAllItemsInSet(self::QUEUE_SET_KEY));
    }

}