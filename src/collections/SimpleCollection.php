<?php

namespace inhere\tools\collections;

use ArrayIterator;
use inhere\tools\Interfaces\CollectionInterface;

/**
 * Collection
 * This class provides a common interface used by many other
 * classes in a inhere\tools application that manage "collections"
 * of data that must be inspected and/or manipulated
 */
class SimpleCollection implements CollectionInterface
{
    /**
     * The source data
     * @var array
     */
    protected $data = [];

    /**
     * @param array $items
     * @return static
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * Create new collection
     * @param array $items Pre-populate collection with this key-value array
     */
    public function __construct($items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /********************************************************************************
     * Collection interface
     *******************************************************************************/

    /**
     * Set collection item
         * @param string $key   The data key
     * @param mixed  $value The data value
     * @return static
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function add($name, $value)
    {
        if ( !$this->has($name) ) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Get collection item for key
         * @param string $key     The data key
     * @param mixed  $default The default value to return if data key does not exist
         * @return mixed The key's value, or the default value
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * Add item to collection
         * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param array $names
     * @return $this
     */
    public function gets(array $names)
    {
        $values = [];

        foreach ($names as $name ) {
            $values[] = $this->get($name);
        }

        return $values;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function sets(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get all items in collection
         * @return array The collection's source data
     */
    public function all()
    {
        return $this->data;
    }

    public function toArray()
    {
        return $this->all();
    }

    /**
     * Get collection keys
         * @return array The collection's source data keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Does this collection have a given key?
         * @param string $key The data key
         * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove item from collection
         * @param string $key The data key
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Remove all items from collection
     */
    public function clear()
    {
        $this->data = [];
    }

    /********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
         * @param  string $key The data key
         * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get collection item for key
         * @param string $key The data key
         * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set collection item
         * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Remove item from collection
         * @param string $key The data key
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /********************************************************************************
     * Countable interface
     *******************************************************************************/

    /**
     * Get number of items in collection
         * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /********************************************************************************
     * JsonSerializable interface
     *******************************************************************************/

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return json_encode($this->data);
    }

    /********************************************************************************
     * Serializable interface
     *******************************************************************************/

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }

    /********************************************************************************
     * IteratorAggregate interface
     *******************************************************************************/

    /**
     * Get collection iterator
         * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /********************************************************************************
     * Magic method
     *******************************************************************************/

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }
}
