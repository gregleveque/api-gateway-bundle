<?php


namespace Gie\Gateway\Psr7;


class Response extends \GuzzleHttp\Psr7\Response
{
    private $body;

    public function saveBody() {
        $new = clone $this;
        $new->body = parent::getBody();

        return $new;
    }

    public function dumpBody()
    {
        return $this->body ?? '';
    }
}