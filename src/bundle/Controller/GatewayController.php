<?php

namespace Gie\GatewayBundle\Controller;



use Gie\Gateway\Cache\RequestHash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as ServerRequest;
use Symfony\Component\HttpFoundation\Response;

class GatewayController
{
    public function indexAction(string $endpoint, ServerRequest $serverRequest)
    {
        $requestHeaders = [];
        $forwardHeaders = \explode(',', $serverRequest->headers->get('x-gateway-forward', ''));

        if ($forwardHeaders) {
            $serverHeaders = $serverRequest->headers->all();
            $requestHeaders = array_filter($serverHeaders, function ($header) use ($forwardHeaders) {
                return in_array($header, $forwardHeaders);
            });
        }


        $requestMethod = $serverRequest->getMethod();
        $requestUri = $serverRequest->query->count()
            ? $endpoint . '?' . $serverRequest->getQueryString()
            : $endpoint;

        $requestId = \implode('|', [
            $requestMethod,
            $requestUri, // sort params
            \serialize($requestHeaders)
        ]);

        $requestHash = substr(str_replace('/', '-',base64_encode(hash('sha256', $requestId, true))), 0, 10);

        $request =  new Request($serverRequest->getMethod(), $requestUri, $requestHeaders);

        $client = new Client();
        $promise = $client->send($request);

        return new Response($promise->getBody(), $promise->getStatusCode(), $promise->getHeaders());
    }

}