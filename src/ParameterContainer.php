<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

class ParameterContainer
{

    private $parameters;

    private $container;

    public function __construct(Container $container, ArrayResolver $parameters)
    {
        $this->container = $container;
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     */
    public function set($key, $value)
    {
        $path = explode('.', $key);
        $this->validateParameter($key, $value);
        $root = $this->buildParameterContainer($path, $value);

        $this->parameters = $this->parameters->mergeRecursiveUnique($root);
    }

    private function buildParameterContainer(array $path, $value)
    {
        $root = array();
        $current = & $root;

        foreach ($path as $subNode) {
            $current[$subNode] = array();
            $current = &$current[$subNode];
        }

        $current = $value;

        return new ArrayResolver($root);
    }

    public function has($key)
    {
        return $this->parameters->resolve($key, false) !== false;
    }

    /**
     * @param string $key
     */
    public function get($key)
    {
        $value = $this->extractValue($key);

        $this->resolveParameterExpression($value);

        if (is_array($value)) {
            $callback = array(
                $this,
                'resolveParameterExpression'
            );
            array_walk_recursive($value, $callback);
        }

        return $value;
    }

    /**
     * @param string $key
     */
    private function extractValue($key)
    {
        $value = $this->parameters->resolve($key, null);

        if (is_null($value)) {
            throw new UnknownParameterException($key);
        }

        if ($value instanceof ArrayResolver) {
            $value = $value->extract();
        }

        return $value;
    }

    private function resolveParameterExpression(& $item)
    {
        if (! is_string($item)) {
            return;
        }
        // Temporary test : resolve all parameters as references (resolver should return identical
        // value when it is not a reference. Uncomment this condition to selectively resolve $item :
        // if (strpos($item, '$env') === 0 || strpos($item, '$const') === 0) {
        $item = $this->container->resolve($item);
        // }
    }

    /**
     * Check that the value to bind is a scalar, or an array multi-dimensional of scalars
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     *
     * @throws IllegalTypeException
     *
     */
    protected function validateParameter($key, $value)
    {
        if ($this->isSafeValue($value)) {
            return true;
        }

        $this->enforceNonObjectValue($key, $value);
        $this->enforceMultidimensionalArrayOfScalars($key, $value);

        return true;
    }

    private function isSafeValue($value)
    {
        return is_scalar($value);
    }

    /**
     * @param string $key
     */
    private function enforceNonObjectValue($key, $value)
    {
        if (is_object($value)) {
            throw new IllegalTypeException(sprintf("Can't bind parameter %s with a callable", $key));
        }
    }

    /**
     * @param string $key
     */
    private function enforceMultidimensionalArrayOfScalars($key, $value)
    {
        if (is_array($value)) {
            array_walk_recursive($value, $this->getArrayValueValidationCallback($key));
        }
    }

    /**
     * @param string $key
     */
    private function getArrayValueValidationCallback($key)
    {
        return function ($item, $storeKey) use($key)
        {
            if (! is_scalar($item)) {
                throw new IllegalTypeException(sprintf("Can't bind parameter, unauthorized value on key '%s' of '%s'", $storeKey, $key));
            }
        };
    }
}
