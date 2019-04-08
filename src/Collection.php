<?php

namespace Brueggern\CrestronFusionHandler;

use Brueggern\CrestronFusionHandler\Exceptions\CollectionException;

class Collection
{
    /**
     * Array of objects
     *
     * @var array
     */
    protected $items = [];

    /**
     * Add an object to the collection
     *
     * @param mixed $obj
     * @param string $key
     * @return void
     */
    public function addItem(mixed $obj, string $key = null)
    {
        if ($key === null) {
            $this->items[] = $obj;
        }
        else {
            if (isset($this->items[$key])) {
                throw new CollectionException('Key '.$key.' already in use.');
            }
            $this->items[$key] = $obj;
        }
    }

    /**
     * Delete an item from the collection
     *
     * @param string $key
     * @return void
     */
    public function deleteItem(string $key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
        else {
            throw new CollectionException('Invalid key '.$key.'.');
        }
    }

    /**
     * Get an item from the collection
     *
     * @param string $key
     * @return mixed
     */
    public function getItem(string $key) : mixed
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        throw new CollectionException('Invalid key '.$key.'.');
    }

    /**
     * Return keys only
     *
     * @return array
     */
    public function keys() : array
    {
        return array_keys($this->items);
    }

    /**
     * Get length of collection
     *
     * @return int
     */
    public function length() : int
    {
        return count($this->items);
    }

    /**
     * Determine if key exists
     *
     * @param string $key
     * @return bool
     */
    public function keyExists(string $key) : bool
    {
        return isset($this->items[$key]);
    }
}
