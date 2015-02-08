<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Activators\DefaultActivatorFactory;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\ServiceBuilder\DefaultServiceBuilder;
use Aztech\Phinject\ServiceBuilder\LazyServiceBuilder;
use Aztech\Phinject\Util\ArrayResolver;

class ServiceBuilderFactory
{
    public function build(ArrayResolver $options)
    {
        $activatorFactory = new DefaultActivatorFactory();
        $injectorFactory = new InjectorFactory();

        if ((bool) $options->resolve('deferred', true) == true) {
            $serviceBuilder = new LazyServiceBuilder($activatorFactory, $injectorFactory);
        }
        else {
            $serviceBuilder = new DefaultServiceBuilder($activatorFactory, $injectorFactory);
        }

        return $serviceBuilder;
    }
}
