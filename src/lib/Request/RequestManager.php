<?php


namespace Gie\Gateway\Request;

use Gie\Gateway\Cache\CacheManager;
use Gie\GatewayBundle\Event\Events;
use Gie\GatewayBundle\Event\ResponseEvent;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class RequestManager
{
    /** @var CacheManager  */
    protected $cacheManager;

    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    public function __construct(CacheManager $cacheManager, EventDispatcherInterface $dispatcher)
    {
        $this->cacheManager = $cacheManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RequestInterface $request
     * @return Response
     */
    public  function sendRequest(RequestInterface $request)
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
            $this->cacheManager->saveResponse($requestId, $response);
        }

        $this->dispatcherHelper($request, $response);

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param $response
     */
    private function dispatcherHelper(RequestInterface $request, $response)
    {

        $eventname = $request instanceof DeferredRequest
            ? $request->getEvent()
            : Events::RESPONSE;

        if (Kernel::VERSION_ID < 40300) {
            $this->dispatcher->dispatch($eventname, new ResponseEvent($response));
        } else {
            $this->dispatcher->dispatch(new ResponseEvent($response), $eventname);
        }

    }
}