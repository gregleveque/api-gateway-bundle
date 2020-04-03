<?php

namespace Gie\Gateway\Cache\Adapter;


use Symfony\Component\Cache\Adapter\AdapterInterface;

interface SetAdapterInterface extends AdapterInterface
{

    /**
     * Adds a members to the set value stored at key.
     *
     * @param string $key
     * @param mixed ...$members
     * @return bool|false|int|mixed
     */
    public function addItemsInSet(string $key, ...$members);

    /**
     * Removes and returns a random element from the set value at Key.
     *
     * @param string $key
     * @return array|bool|mixed|string
     */
    public function popRandomItemInSet(string $key);

    /**
     * Removes and returns all elements from the set value at Key.
     *
     * @param string $key
     * @return array|bool|mixed|string
     */
    public function getAllItemsInSet(string $key);

}