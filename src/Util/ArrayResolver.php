<?php

namespace Aztech\Phinject\Util;

use Aztech\Phinject\Iterator;

class ArrayResolver extends Iterator
{

    private static $merger = null;

    public function __construct(array $source = null)
    {
        if (self::$merger == null) {
            self::$merger = new Arrays();
        }

        if ($source == null) {
            $source = array();
        }

        $this->source = & $source;
    }

    public function current()
    {
        return $this->wrapIfNecessary(parent::current(), false);
    }

    public function extract()
    {
        return $this->source;
    }

    public function extractKeys()
    {
        return array_keys($this->source);
    }

    public function merge(ArrayResolver $array)
    {
        return new self(array_merge($this->source, $array->extract()));
    }

    public function mergeRecursiveUnique(ArrayResolver $array)
    {
        return new self(self::$merger->mergeRecursiveUnique($this->source, $array->extract()));
    }

    /**
     * Resolves a value stored in an array, optionally by using dot notation to access nested elements.
     *
     * @param string $key The key value to resolve.
     * @param mixed $default
     * @param bool $coerceArray
     * @return ArrayResolver|mixed The resolved value or the provided default value. If the resolved value is an array, it will be wrapped as an ArrayResolver instance.
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function resolve($key, $default = null, $coerceArray = false)
    {
        $toReturn = $default;
        $dotted = explode(".", $key);

        if (count($dotted) > 1) {
            $toReturn = $this->walkNameComponents($dotted, $default, false);
        }
        elseif (array_key_exists($key, $this->source)) {
            $toReturn = $this->source[$key];
        }

        return $this->wrapIfNecessary($toReturn, $coerceArray);
    }

    public function resolveStrict($key)
    {
        $dotted = explode(".", $key);

        if (count($dotted) > 1) {
            try {
                return $this->wrapIfNecessary($this->walkNameComponents($dotted, null, true));
            }
            catch (\InvalidArgumentException $exception) {
                throw new \InvalidArgumentException(sprintf('Required key "%s" is not set.', $key));
            }
        }

        if (array_key_exists($key, $this->source)) {
            return  $this->wrapIfNecessary($this->source[$key]);
        }

        throw new \InvalidArgumentException(sprintf('Required key "%s" is not set.', $key));
    }

    private function walkNameComponents(array $dotted, $default, $strict)
    {
        $currentDepthData = $this->source;

        foreach ($dotted as $paramKey) {
            if (! array_key_exists($paramKey, $currentDepthData)) {
                if (! $strict) {
                    return $default;
                }

                throw new \InvalidArgumentException('Key does not exist');
            }

            $currentDepthData = $currentDepthData[$paramKey];
        }

        return $currentDepthData;
    }

    /**
     *
     * @param string $value
     * @param bool $coerceArray
     * @return mixed
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function wrapIfNecessary($value, $coerceArray = false)
    {
        if (! is_array($value) && $coerceArray == true) {
            $value = [ $value ];
        }

        if (is_array($value)) {
            return new static($value);
        }

        return $value;
    }
}
