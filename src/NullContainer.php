<?php

namespace Aztech\Phinject;

class NullContainer implements Container
{
    /**
     * (non-PHPdoc)
     * @see \Interop\Container\ContainerInterface::has()
     */
    public function has($id)
    {
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Interop\Container\ContainerInterface::get()
     */
    public function get($id)
    {
        throw new UnknownDefinitionException($id);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Container::getNamespace()
     */
    public function getNamespace($namespace)
    {
        throw new UnknownDefinitionException($namespace);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Container::getParameter()
     */
    public function getParameter($parameterName)
    {
        throw new UnknownDefinitionException($parameterName);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Container::hasParameter()
     */
    public function hasParameter($parameterName)
    {
        return false;
    }
}
