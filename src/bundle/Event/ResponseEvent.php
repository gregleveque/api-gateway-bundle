<?php


namespace Gie\GatewayBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ResponseEvent extends Event
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

}