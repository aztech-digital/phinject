<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Injector;
use Aztech\Phinject\Util\ArrayResolver;

class PropertyInjector implements Injector
{
    public function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $propConfig = $serviceConfig->resolve('props', $serviceConfig->resolve('properties', []));

        foreach($propConfig as $propName => $propValue) {
            $service->$propName = $container->resolve($propValue);
        }

        return true;
    }
}
