<?php

namespace Gie\Gateway\API\Request;

use Guzzle\Http\QueryAggregator\QueryAggregatorInterface;
use Psr\Http\Message\RequestInterface;

interface RequestHelperInterface
{
    /**
     * Generate request ID hash
     *
     * @param RequestInterface $request
     * @return string
     */
    public static function hash(RequestInterface $request): string;

    /**
     * Build query string.
     *
     * @param array $query
     * @param QueryAggregatorInterface $aggregator
     * @return string
     */
    public static function  getQueryString(array $query, QueryAggregatorInterface $aggregator): string;

    /**
     * Get chosen Aggregator
     *
     * @param string $value
     * @return QueryAggregatorInterface
     */
    public static function getAggregator(string $value): QueryAggregatorInterface;
}