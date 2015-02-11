<?php

namespace Aztech\Phinject\Util;

use Aztech\Phinject\Container;

class MethodInvocationDefinition
{

    private $owner;

    private $name;

    private $isStatic;

    private $areArgsDefined;

    private $args;

    /**
     * @param boolean $static
     */
    public function __construct($owner, $name, $static, array $args = null)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->isStatic = $static;
        $this->areArgsDefined = ($args !== null);
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function isStatic()
    {
        return $this->isStatic;
    }

    public function hasArguments()
    {
        return $this->areArgsDefined;
    }

    public function getArguments()
    {
        return $this->args;
    }

    public function extractArguments(Container $container, ArrayResolver $serviceConfig)
    {
        if ($this->hasArguments()) {
            return $container->resolveMany($this->getArguments());
        }

        if (isset($serviceConfig['arguments'])) {
            return $container->resolveMany($serviceConfig->resolveArray('arguments')->extract());
        }

        return [];
    }

    public function equals(MethodInvocationDefinition $other)
    {
        if ($other->getName() != $this->getName()) {
            return false;
        }

        if ($other->getOwner() != $this->getOwner()) {
            return false;
        }

        return count(array_diff($this->getArguments(), $other->getArguments())) == 0;
    }
}
