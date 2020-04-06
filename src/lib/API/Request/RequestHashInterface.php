<?php

namespace Gie\Gateway\API\Request;

use Psr\Http\Message\RequestInterface;

interface RequestHashInterface
{
    /**
     * Generate request ID hash
     *
     * @param RequestInterface $request
     * @return string
     */
    public static function hash(RequestInterface $request): string;
}