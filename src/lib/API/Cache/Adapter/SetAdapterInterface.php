<?php

namespace Gie\Gateway\API\Cache\Adapter;

use Symfony\Component\Cache\Adapter\AdapterInterface;

interface SetAdapterInterface extends AdapterInterface
{

    /**
     * Adds a members to the set value stored at key.
     *
     * @param string $key
     * @param mixed ...$members
     * @return int
     */
    public function addItemsInSet(string $key, ...$members): int;

    /**
     * Removes and returns a random element from the set value at Key.
     *
     * @param string $key
     * @return array
     */
    public function listAllItemsInSet(string $key): array;

    /**
     * Removes and returns all elements from the set value at Key.
     *
     * @param string $key
     * @return array
     */
    public function getAllItemsInSet(string $key): array;

}