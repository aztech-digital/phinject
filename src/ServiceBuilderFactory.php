<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Activators\DefaultActivatorFactory;
use Aztech\Phinject\Activators\Lazy\LazyActivatorFactory;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\Util\ArrayResolver;

class ServiceBuilderFactory
{
    public function build(ArrayResolver $options)
    {
        $activatorFactory = new DefaultActivatorFactory();

        $serviceBuilder = new ServiceBuilder(
            $activatorFactory,
            new InjectorFactory()
        );

        if ((bool) $options->resolve('deferred', true)) {
            $activatorFactory = new LazyActivatorFactory($activatorFactory, $serviceBuilder);
            $serviceBuilder = new ServiceBuilder($activatorFactory);
        }

        return $serviceBuilder;
    }
}
