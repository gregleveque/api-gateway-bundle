<?php

namespace Gie\GatewayBundle\Controller;



use Gie\Gateway\Cache\CacheManager;
use Gie\Gateway\Psr7\DeferredRequest;
use Gie\Gateway\Utils\RequestHash;
use Gie\GatewayBundle\Event\Events;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as ServerRequest;
use Symfony\Component\HttpFoundation\Response;


class GatewayController
{
    /** @var CacheManager */
    protected $cacheManager;

    public function __construct(CacheManager$cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }


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

        $request = new Request($serverRequest->getMethod(), $requestUri, $requestHeaders);
        $requestId = RequestHash::hash($request);;

        if (!$response = $this->cacheManager->getResponse($requestId)) {
            $client = new Client();
            $result = $client->send($request);
            $response = new Response($result->getBody(), $result->getStatusCode(), $result->getHeaders());
            $this->cacheManager->saveResponse($requestId, $response);
        }

        return $response;
    }

}