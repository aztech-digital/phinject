<?php

namespace Aztech\Phinject;

/**
 *
 * @author thibaud
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Iterator implements \ArrayAccess, \Countable, \Iterator
{
    /**
     *
     * @var array
     */
    protected $source = [];

    public function __construct(array $data = array())
    {
        $this->source = $data;
    }

    public function rewind()
    {
        reset($this->source);
    }

    public function current()
    {
        return current($this->source);
    }

    public function key()
    {
        return key($this->source);
    }

    public function next()
    {
        return next($this->source);
    }

    public function valid()
    {
        $key = key($this->source);

        return ($key !== null && $key !== false);
    }

    public function count()
    {
        return count($this->source);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            return $this->source[] = $value;
        }

        return $this->source[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->source[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->source[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->source[$offset]) ? $this->source[$offset] : null;
    }
}
