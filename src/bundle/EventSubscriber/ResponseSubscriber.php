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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
             Events::RESPONSE => [
                ['logpResponse', 0],
            ],
            Events::DEFERRED_RESPONSE => [
                ['logDeferredResponse', 0],
            ]
        ];
    }

    /**
     * @param ResponseEvent $response
     */
    public function logResponse(ResponseEvent $response): void
    {
        $this->logger->debug($this->log($response));
    }

    /**
     * @param ResponseEvent $response
     */
    public function logDeferredResponse(ResponseEvent $response): void
    {
        $this->logger->info($this->log($response));
    }

    /**
     * @param ResponseEvent $response
     * @return string
     */
    private function log(ResponseEvent $response): string
    {
        return 'Request with ID "'
            . $response->getId()
            . '" to "'
            . $response->getRequest()->getUri()->getHost()
            . '" was sent.';
    }

}