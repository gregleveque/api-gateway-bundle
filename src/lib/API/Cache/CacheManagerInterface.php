<?php

namespace Gie\Gateway\API\Cache;

use Gie\Gateway\API\Request\RequestWithEventInterface;
use Symfony\Component\HttpFoundation\Response;

interface CacheManagerInterface
{
    /**
     * Save response to cache
     *
     * @param string $key
     * @param Response $response
     * @param int $ttl
     * @return bool
     */
    public function saveResponse(string $key, Response $response, int $ttl = 0): bool;

    /**
     * Get response from cache
     *
     * @param string $key
     * @return Response|null
     */
    public function getResponse(string $key): ?Response;

    /**
     * Save request to cache list
     *
     * @param RequestWithEventInterface $request
     * @return int
     */
    public function deferRequest(RequestWithEventInterface $request): int;

    /**
     * Get and delete all requests from cache list
     *
     * @return RequestWithEventInterface[]
     */
    public function getDeferredRequests(): array;


    /**
     * Get all requests from cache list
     *
     * @return RequestWithEventInterface[]
     */
    public function listDeferredRequests(): array;
}