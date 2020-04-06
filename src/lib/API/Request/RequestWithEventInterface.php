<?php

namespace Gie\Gateway\API\Request;

use Psr\Http\Message\RequestInterface;

interface RequestWithEventInterface extends RequestInterface
{
    public function withEvent(string $eventName): RequestWithEventInterface;

    public function getEvent(): string;
}