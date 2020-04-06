<?php

namespace Gie\Gateway\Core\Cache\Adapter;

use Gie\Gateway\API\Cache\Adapter\SetAdapterInterface;
use Symfony\Component\Cache\Adapter\TraceableAdapter;

class TraceableSetAdapter extends TraceableAdapter implements SetAdapterInterface
{

    public function __construct(SetAdapterInterface $pool)
    {
        parent::__construct($pool);
    }

    /**
     * @inheritdoc
     */
    public function addItemsInSet(string $key, ...$members): int
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->addItemsInSet($key, ...$members);
        } finally {
            $event->end = microtime(true);
        }
    }

    /**
     * @inheritdoc
     */
    public function listAllItemsInSet(string $key): array
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->listAllItemsInSet($key);
        } finally {
            $event->end = microtime(true);
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllItemsInSet(string $key): array
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->getAllItemsInSet($key);
        } finally {
            $event->end = microtime(true);
        }
    }

}