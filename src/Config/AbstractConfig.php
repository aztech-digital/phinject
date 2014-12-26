<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Config;
use Aztech\Phinject\Util\ArrayResolver;

/**
 * Base class for injection configurations.
 *
 * @author Olivier Madre
 * @author Thibaud Fabre
 */
abstract class AbstractConfig implements Config
{

    /**
     *
     * @var mixed[]
     */
    protected $data = array();

    /**
     * Compiles the current configuration to a valid PHP snippet.
     *
     * @return string
     */
    public function compile()
    {
        $this->doLoad();

        $data = (array) $this->data;

        $this->removeKey($data, 'include');
        $this->removeKey($data, '__META__');

        return var_export($data, true);
    }

    /**
     * Unsets a key from an array
     *
     * @param mixed[] $data
     * @param string $key
     */
    private function removeKey(array & $data, $key)
    {
        if (array_key_exists($key, $data)) {
            unset($data[$key]);
        }
    }

    /**
     * Loads and returns the configuration data.
     *
     * @return ArrayResolver
     */
    public function load()
    {
        if (empty($this->data)) {
            $this->data = $this->doLoad();
        }

        return $this->data;
    }

    public function reload()
    {
        $this->data = $this->doLoad();
    }

    /**
     * Returns the already loaded configuration.
     *
     * @return mixed[] The loaded configuration data, or an empty array if no call to load was made.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns an array resolver containing the current configuration data.
     *
     * @return ArrayResolver
     */
    public function getResolver()
    {
        $this->load();

        return new ArrayResolver($this->data);
    }

    /**
     * Implementations must perform the actual configuration parsing/loading when this method is called.
     *
     * @return array
     */
    abstract protected function doLoad();
}
