<?php

namespace Gie\Gateway\Utils;

use GuzzleHttp\Psr7\Request;

class RequestHash
{
    public static function hash(Request $request)
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