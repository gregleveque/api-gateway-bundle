<?php

namespace Gie\Gateway\API\Event;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

interface ResponseEventInterface
{
    /**
     * @return string
     */
    public function getId(): string;


    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;


    /**
     * @return Response
     */
    public function getResponse(): Response;

}