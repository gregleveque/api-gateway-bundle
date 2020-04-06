<?php

namespace Gie\Gateway\Core\Request;

use Gie\Gateway\API\Request\RequestHashInterface;
use Psr\Http\Message\RequestInterface;

class RequestHash implements RequestHashInterface
{
    /**
     * @inheritDoc
     */
    public static function hash(RequestInterface $request): string
    {
        $method = $request->getMethod();
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $port = $request->getUri()->getPort();
        $path = $request->getUri()->getPath();
        $headers = $request->getHeaders();
        $body = $request->getBody()->getContents();

        parse_str($request->getUri()->getQuery(), $query);
        ksort($query);
        ksort($headers);

        $key = \implode('|', [$method, $scheme, $host, $port, $path, \serialize($headers), $body, \serialize($query)]);

        return substr(str_replace('/', '-', base64_encode(hash('sha256', $key, true))), 0, 10);
    }

}