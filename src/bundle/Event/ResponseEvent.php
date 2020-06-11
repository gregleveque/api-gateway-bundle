<?php

namespace Gie\GatewayBundle\Event;

use Gie\Gateway\API\Event\ResponseEventInterface;
use Gie\Gateway\Core\Request\RequestHelper;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ResponseEvent extends Event implements ResponseEventInterface
{
    /** @var string */
    private $id;

    /** @var RequestInterface */
    private $request;

    /** @var Response */
    private $response;

    public function __construct(RequestInterface $request, Response $response)
    {
        $this->id = RequestHelper::hash($request);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

}