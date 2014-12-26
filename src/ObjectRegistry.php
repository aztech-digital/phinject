<?php

namespace Aztech\Phinject;

/**
 * Registry for storing built object instances.
 *
 * @author Olivier Madre
 * @author Thibaud Fabre
 */
class ObjectRegistry
{

    /**
     *
     * @var mixed[]
     */
    protected $data = array();

    /**
     * Flush all stored instances from the registry.
     *
     * @return void
     */
    public function flush()
    {
        $this->data = array();
    }

    /**
     * Fetches an object from the registry.
     *
     * @param string $key
     * @return mixed
     */
    public function & get($key)
    {
        if (! $this->has($key)) {
            // Assignment required for by-ref return
            $null = null;

            return $null;
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     */
    public function & getStrict($key)
    {
        if (! $this->has($key)) {
            throw new \RuntimeException('Key ' . $key . ' not found in DI Container registry');
        }

        return $this->data[$key];
    }

    /**
     * Returns a boolean indicating whether there is an object associated to a given key in the registry.
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Stores an object instance in the registry.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Stores an object reference in the registry.
     *
     * @param string $key
     * @param mixed $value
     */
    public function rset($key, & $value)
    {
        $this->data[$key] = & $value;
    }
}
