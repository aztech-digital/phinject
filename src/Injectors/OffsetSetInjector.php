<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Injector;
use Aztech\Phinject\Util\ArrayResolver;

class OffsetSetInjector implements Injector
{
    public function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $propConfig = $serviceConfig->resolveArray('set', []);

        foreach($propConfig as $propName => $propValue) {
            $service[$propName] = $container->resolve($propValue);
        }

        return true;
    }
}
