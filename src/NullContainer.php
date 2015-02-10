<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;

class NullContainer implements ContainerInterface
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
}
