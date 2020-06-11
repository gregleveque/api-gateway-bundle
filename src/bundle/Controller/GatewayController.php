<?php

namespace Gie\GatewayBundle\Controller;

use Gie\Gateway\API\Request\RequestManagerInterface;
use Gie\Gateway\Core\Request\RequestHelper;
use Guzzle\Http\QueryAggregator\QueryAggregatorInterface;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Response;

class GatewayController
{
    /** @var RequestManagerInterface */
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
     * @param QueryAggregatorInterface $aggregator
     * @param int|null $ttl
     * @return Response
     */
    public function __invoke(
        string $method,
        string $target,
        array $query,
        array $headers,
        QueryAggregatorInterface $aggregator,
        int $ttl = null
    )
    {
        return $this->requestManager->sendRequest(
            new Request($method, $target . RequestHelper::getQueryString($query, $aggregator), $headers),
            $ttl
        );
    }

}