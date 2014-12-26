<?php

namespace Aztech\Phinject\Activators\Reflection;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;

class AliasActivator implements Activator
{
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $alias = $serviceConfig->resolve('alias', null);

        return $container->resolve($alias);
    }
}
