<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;

class NamespaceResolver implements Resolver
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function accepts($reference)
    {
        return strpos($reference, '$ns:', 0) === 0;
    }

    public function resolve($reference)
    {
        return $this->container->getNamespace(substr($reference, 4));
    }
}
