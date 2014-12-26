<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;

class ParameterResolver implements Resolver
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function accepts($reference)
    {
        return substr($reference, 0, 1) === '%';
    }

    public function resolve($reference)
    {
        return $this->container->getParameter(substr($reference, 1));
    }
}
