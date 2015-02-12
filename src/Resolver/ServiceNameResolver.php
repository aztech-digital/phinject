<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;
class ServiceNameResolver implements Resolver
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Resolver\Resolver::accepts()
     */
    public function accepts($reference)
    {
        return $reference == '$name';
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Resolver\Resolver::resolve()
     */
    public function resolve($reference)
    {
        return $this->container->getBuildContextName();
    }
}