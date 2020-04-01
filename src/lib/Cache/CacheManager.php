<?php

namespace Gie\Gateway\Cache;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheManager
{
    /** @var int  */
    const DEFAULT_TTL = 60;

    /** @var AdapterInterface  */
    protected $cache;

    /** @var int  */
    protected $default_ttl;

    public function __construct(AdapterInterface $cache, $default_ttl = self::DEFAULT_TTL)
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
     * @return bool|mixed
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

    public function pushRequest($key, $request)
    {

    }

}