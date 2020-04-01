<?php


namespace Gie\GatewayBundle\Event;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\EventDispatcher\Event;

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