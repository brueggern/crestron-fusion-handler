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
     * @param mixed $key
     * @return void
     */
    public function addItem($obj, $key = null)
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
     * Get an item from the collection
     *
     * @param mixed $key
     * @return mixed
     */
    public function getItem($key)
    {
        if (is_string($key)) {
            if (isset($this->items[$key])) {
                return $this->items[$key];
            }
            throw new CollectionException('Invalid key '.$key.'.');
        }

        if (is_integer($key)) {
            $values = array_values($this->items);
            if (isset($values[$key])) {
                return $values[$key];
            }
            throw new CollectionException('Invalid key '.$key.'.');
        }

        throw new CollectionException('Invalid key type (only string or numeric key is allowed).');
    }

    /**
     * Delete an item from the collection
     *
     * @param mixed $key
     * @return bool
     */
    public function deleteItem($key) : bool
    {
        if (is_string($key)) {
            if (isset($this->items[$key])) {
                unset($this->items[$key]);
                return true;
            }

            throw new CollectionException('Invalid key '.$key.'.');
        }

        if (is_integer($key)) {
            $keys = array_keys($this->items);
            $key = $keys[$key];
            if (isset($this->items[$key])) {
                unset($this->items[$key]);
                return true;
            }

            throw new CollectionException('Invalid key '.$key.'.');
        }

        throw new CollectionException('Invalid key type (only string or numeric key is allowed).');
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
     * Return values only
     *
     * @return array
     */
    public function values() : array
    {
        return array_values($this->items);
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

    /**
     * Returns the item array
     *
     * @return array
     */
    public function get() : array
    {
        return $this->items;
    }
}
