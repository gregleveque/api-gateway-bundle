<?php

namespace Gie\Gateway\Core\Cache;

use Gie\Gateway\API\Cache\Adapter\SetAdapterInterface;
use Gie\Gateway\API\Cache\CacheManagerInterface;
use Gie\Gateway\API\Request\RequestWithEventInterface;
use Symfony\Component\HttpFoundation\Response;

class CacheManager implements CacheManagerInterface
{
    /** @var int  */
    const DEFAULT_TTL = 60;

    private const QUEUE_SET_KEY = 'deferred-requests';

    /** @var SetAdapterInterface  */
    protected $cachePool;

    /** @var int  */
    protected $default_ttl;

    public function __construct(SetAdapterInterface $cachePool, int $default_ttl = self::DEFAULT_TTL)
    {
        $this->cachePool = $cachePool;
        $this->default_ttl = $default_ttl;
    }

    /**
     * @inheritDoc
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function saveResponse(string $key, Response $response, int $ttl = self::DEFAULT_TTL): bool
    {
        $item = $this->cachePool->getItem($key)
            ->set($response)
            ->expiresAfter($ttl);

        return $this->cachePool->save($item);
    }

    /**
     * @inheritDoc
     */
    public function getResponse(string $key): ?Response
    {
        $item = $this->cachePool->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function deferRequest(RequestWithEventInterface $request): int
    {
        return $this->cachePool->addItemsInSet(self::QUEUE_SET_KEY, \serialize($request));
    }

    /**
     * @inheritDoc
     */
    public function getDeferredRequests(): array
    {
        return array_map(function ($request) {
            return \unserialize($request);
        }, $this->cachePool->getAllItemsInSet(self::QUEUE_SET_KEY));
    }

    /**
     * @inheritDoc
     */
    public function listDeferredRequests(): array
    {
        return array_map(function ($request) {
            return \unserialize($request);
        }, $this->cachePool->listAllItemsInSet(self::QUEUE_SET_KEY));
    }

}