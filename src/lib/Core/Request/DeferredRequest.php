<?php

namespace Gie\Gateway\Core\Request;

use Gie\Gateway\API\Request\RequestWithEventInterface;
use Gie\GatewayBundle\Event\Events;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class DeferredRequest extends Request implements RequestWithEventInterface
{
    private $event = Events::DEFERRED_RESPONSE;

    public function withEvent(string $eventName): RequestWithEventInterface
    {
        if ($eventName === $this->event) {
            return $this;
        }

        $new = clone $this;
        $new->event = $eventName;

        return $new;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

}