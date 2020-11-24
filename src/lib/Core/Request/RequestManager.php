<?php

namespace Gie\Gateway\Core\Request;

use Gie\Gateway\API\Cache\CacheManagerInterface;
use Gie\Gateway\API\Request\RequestManagerInterface;
use Gie\GatewayBundle\Event\Events;
use Gie\GatewayBundle\Event\ResponseEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class RequestManager implements RequestManagerInterface
{
    /** @var CacheManagerInterface */
    protected $cacheManager;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    public function __construct(CacheManagerInterface $cacheManager, EventDispatcherInterface $dispatcher)
    {
        $this->cacheManager = $cacheManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RequestInterface $request
     * @param int $ttl
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(RequestInterface $request, int $ttl): Response
    {
        $requestId = RequestHelper::hash($request);

        if (!$response = $this->cacheManager->getResponse($requestId)) {
            $client = new Client();
            $result = $client->send($request);
            $response = new Response(
                $result->getBody(),
                $result->getStatusCode(),
                $result->getHeaders()
            );
            $this->cacheManager->saveResponse($requestId, $response, $ttl);
        }

        $this->dispatcherHelper($request, $response);

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param Response $response
     */
    private function dispatcherHelper(RequestInterface $request, Response $response): void
    {

        $eventName = $request instanceof DeferredRequest
            ? $request->getEvent()
            : Events::RESPONSE;

        if (Kernel::VERSION_ID < 40300) {
            $this->dispatcher->dispatch($eventName, new ResponseEvent($request, $response));
        } else {
            $this->dispatcher->dispatch(new ResponseEvent($request, $response), $eventName);
        }

    }

    /**
     * @param array $requests
     * @param int $ttl
     * @param int $nbRequests
     * @return array
     */
    public function sendConcurrentRequests(array $requests, int $ttl, int $nbRequests = 5): array
    {
        $result = [];
        $hash = [];
        $cached = true;

        foreach ($requests as $request) {
            $requestId = RequestHelper::hash($request);
            $hash[] = $requestId;

            if ($response = $this->cacheManager->getResponse($requestId)) {
                $result[] = $response;
            } else {
                $cached = false;
            }
        }

        if ($cached) {
            return array_combine(array_keys($requests), array_values($result));
        }

        $process = function () use ($requests) {
            foreach ($requests as $request) {
                if ($request instanceof RequestInterface) {
                    yield $request;
                }
            }
        };

        $pool = new Pool(new Client(), $process(), [
            'concurrency' => $nbRequests,
            'fulfilled' => function (\GuzzleHttp\Psr7\Response $response, $index) use (&$result, $hash, $ttl) {
                $res = new Response(
                    $response->getBody(),
                    $response->getStatusCode(),
                    $response->getHeaders()
                );
                $this->cacheManager->saveResponse($hash[$index], $res, $ttl);
                $result[$index] = $res;
            },
            'rejected' => function (RequestException $reason, $index) {
                throw $reason;
            },
        ]);

        $promise = $pool->promise();

        $promise->wait();

        return array_combine(array_keys($requests), array_values($result));
    }
}