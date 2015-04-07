<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;

class NotResolver implements Resolver
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    function accepts($reference)
    {
        return substr(trim($reference), 0, 1) == '!';
    }

    function resolve($reference)
    {
        return ! $this->container->resolve(substr($reference, 1));
    }
}