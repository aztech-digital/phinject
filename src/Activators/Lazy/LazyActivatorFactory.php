<?php

namespace Aztech\Phinject\Activators\Lazy;

use Aztech\Phinject\Activator;
use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\ServiceBuilder;
use Aztech\Phinject\Util\ArrayResolver;

class LazyActivatorFactory implements ActivatorFactory
{
    private $activator;

    private $factory;

    private $serviceBuilder;

    public function __construct(ActivatorFactory $factory, ServiceBuilder $builder)
    {
        $this->factory = $factory;
        $this->serviceBuilder = $builder;

        $this->activator = new LazyActivator($this->serviceBuilder);
    }

    function addActivator($key, Activator $activator)
    {
        $this->factory->addActivator($key, $activator);
    }

    function getActivator($serviceName, ArrayResolver $configuration)
    {
        return $this->activator;
    }
}
