<?php

namespace Gie\Gateway\Core\Request;

use Gie\Gateway\API\Cache\CacheManagerInterface;
use Gie\Gateway\API\Request\RequestManagerInterface;
use Gie\GatewayBundle\Event\Events;
use Gie\GatewayBundle\Event\ResponseEvent;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class RequestManager implements RequestManagerInterface
{
    /** @var CacheManagerInterface  */
    protected $cacheManager;

    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    public function __construct(CacheManagerInterface $cacheManager, EventDispatcherInterface $dispatcher)
    {
        $this->cacheManager = $cacheManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RequestInterface $request
     * @return Response
     */
    public function sendRequest(RequestInterface $request, int $ttl): Response
    {
        $requestId = RequestHash::hash($request);

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

        $eventname = $request instanceof DeferredRequest
            ? $request->getEvent()
            : Events::RESPONSE;

        if (Kernel::VERSION_ID < 40300) {
            $this->dispatcher->dispatch($eventname, new ResponseEvent($request, $response));
        } else {
            $this->dispatcher->dispatch(new ResponseEvent($request, $response), $eventname);
        }

    }
}