<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Activators\DefaultActivatorFactory;
use Aztech\Phinject\Activators\Lazy\LazyActivatorFactory;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\Util\ArrayResolver;

class ServiceBuilderFactory
{
    private $serviceBuilder;

    public function __construct(ServiceBuilder $serviceBuilder = null)
    {
        if ($serviceBuilder === null) {
            $serviceBuilder = new ServiceBuilder(
                new DefaultActivatorFactory(),
                new InjectorFactory()
            );
        }

        $this->serviceBuilder = $serviceBuilder;
    }

    public function build(ArrayResolver $options)
    {
        if ((bool) $options->resolve('deferred', true) && false) {
            $activatorFactory = new LazyActivatorFactory($activatorFactory, $serviceBuilder);
            $serviceBuilder = new ServiceBuilder($activatorFactory);
        }

        return $this->serviceBuilder;
    }
}
