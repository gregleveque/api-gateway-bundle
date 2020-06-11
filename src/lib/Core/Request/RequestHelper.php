<?php

namespace Gie\Gateway\Core\Request;

use Gie\Gateway\API\Request\RequestHelperInterface;
use Guzzle\Http\QueryAggregator\CommaAggregator;
use Guzzle\Http\QueryAggregator\DuplicateAggregator;
use Guzzle\Http\QueryAggregator\PhpAggregator;
use Guzzle\Http\QueryAggregator\QueryAggregatorInterface;
use Guzzle\Http\QueryString;
use Psr\Http\Message\RequestInterface;

class RequestHelper implements RequestHelperInterface
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

    /**
     * @inheritDoc
     */
    public static function getQueryString(array $query, QueryAggregatorInterface $aggregator = null): string
    {
        if (count($query)) {
            $queryString = new QueryString($query);
            $queryString->setAggregator($aggregator ?? new DuplicateAggregator());
            return '?' . $queryString->__toString();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public static function getAggregator(string $value): QueryAggregatorInterface
    {
        switch ($value) {
            case 'duplicate':
                return new DuplicateAggregator();
            case 'comma':
                return new CommaAggregator();
            case 'array':
            default:
                return new PhpAggregator();
        }
    }

}