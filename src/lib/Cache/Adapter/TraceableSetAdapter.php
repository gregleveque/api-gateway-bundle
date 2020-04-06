<?php

namespace Gie\Gateway\Cache\Adapter;


use Symfony\Component\Cache\Adapter\TraceableAdapter;


class TraceableSetAdapter extends TraceableAdapter implements SetAdapterInterface
{

    public function __construct(SetAdapterInterface $pool)
    {
        parent::__construct($pool);
    }

    /**
     * {@inheritdoc}
     */
    public function addItemsInSet(string $key, ...$members)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->addItemsInSet($key, ...$members);
        } finally {
            $event->end = microtime(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function listAllItemsInSet(string $key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->listAllItemsInSet($key);
        } finally {
            $event->end = microtime(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllItemsInSet(string $key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->getAllItemsInSet($key);
        } finally {
            $event->end = microtime(true);
        }
    }

}