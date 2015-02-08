<?php

namespace Aztech\Phinject\ServiceBuilder;

use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\Container;
use Aztech\Phinject\ServiceBuilder;
use Aztech\Phinject\Activators\Lazy\LazyActivatorFactory;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\Util\ArrayResolver;

class LazyServiceBuilder implements ServiceBuilder
{
    private $serviceBuilder;

    public function __construct(ActivatorFactory $activatorFactory = null, InjectorFactory $injectorFactory = null)
    {
        $activatorFactory = $activatorFactory ?: new ActivatorFactory();
        $injectorFactory = $injectorFactory ?: new InjectorFactory();

        $serviceBuilder = new DefaultServiceBuilder($activatorFactory, $injectorFactory);
        $activatorFactory = new LazyActivatorFactory($activatorFactory, $serviceBuilder);

        $this->serviceBuilder = new DefaultServiceBuilder($activatorFactory);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\ServiceBuilder::buildService()
     */
    public function buildService(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        return $this->serviceBuilder->buildService($container, $serviceConfig, $serviceName);
    }
}
