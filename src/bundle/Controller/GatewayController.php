<?php

namespace Gie\GatewayBundle\Controller;

use Gie\Gateway\API\Request\RequestManagerInterface;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Request as ServerRequest;
use Symfony\Component\HttpFoundation\Response;

class GatewayController
{
    /** @var RequestManagerInterface  */
    protected $requestManager;

    public function __construct(RequestManagerInterface $requestManager)
    {
        $this->requestManager = $requestManager;
    }

    /**
     * @param string $method
     * @param string $target
     * @param array $query
     * @param array $headers
     * @param array $options
     * @return Response
     */
    public function __invoke(string $method, string $target, array $query, array $headers, int $ttl = null)
    {
        $requestUri = count($query)
            ? $target . '?' . http_build_query($query)
            : $target;

        return $this->requestManager->sendRequest(new Request($method, $requestUri, $headers), $ttl);
    }

}