<?php


namespace Gie\Gateway\Psr7;

use Gie\GatewayBundle\Event\Events;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;


class DeferredRequest extends Request implements RequestInterface
{
    private $event = Events::DEFERRED_RESPONSE;

    public function withEvent(string $eventName)
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