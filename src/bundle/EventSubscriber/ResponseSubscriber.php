<?php

namespace Gie\GatewayBundle\EventSubscriber;


use Gie\GatewayBundle\Event\Events;
use Gie\GatewayBundle\Event\ResponseEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResponseSubscriber implements EventSubscriberInterface
{

    /** @var LoggerInterface */
    protected $logger;

    public static function getSubscribedEvents()
    {
        return [
             Events::RESPONSE => [
                ['dumpResponse', 0],
            ],
            Events::DEFERRED_RESPONSE => [
                ['dumpDeferredResponse', 0],
            ]
        ];
    }

    public function dumpResponse(ResponseEvent $response)
    {
        dump($response->getResponse());
    }

    public function dumpDeferredResponse(ResponseEvent $response)
    {
        dump('DEFERRED', $response->getResponse());
    }

}