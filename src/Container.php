<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;
use Guzzle\Common\Exception\RuntimeException;

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

    /**
     * Returns the name of the object currently being built
     *
     * @return string
     * @throws RuntimeException if there is no active build context
     */
    public function getBuildContextName();
}
