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
     * @param string $endpoint
     * @param ServerRequest $serverRequest
     * @return bool|Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __invoke(string $endpoint, ServerRequest $serverRequest)
    {
        $requestHeaders = [];
        $forwardHeaders = \explode(',', $serverRequest->headers->get('x-gateway-forward', ''));

        if ($forwardHeaders) {
            $serverHeaders = $serverRequest->headers->all();
            $requestHeaders = array_filter($serverHeaders, function ($header) use ($forwardHeaders) {
                return in_array($header, $forwardHeaders);
            });
        }

        $requestUri = $serverRequest->query->count()
            ? $endpoint . '?' . $serverRequest->getQueryString()
            : $endpoint;

        return $this->requestManager->sendRequest(
            new Request($serverRequest->getMethod(), $requestUri, $requestHeaders)
        );
    }

}