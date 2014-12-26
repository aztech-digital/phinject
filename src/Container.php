<?php

namespace Aztech\Phinject;

interface Container extends ReferenceResolver
{

    public function has($serviceName);

    public function get($serviceName);

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
