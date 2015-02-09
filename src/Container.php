<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;

interface Container extends ReferenceResolver, ContainerInterface
{

    /**
     * @param string $namespace
     */
    public function getNamespace($namespace);

    /**
     * @param string $parameterName
     */
    public function getParameter($parameterName);

    public function hasParameter($parameterName);
}
