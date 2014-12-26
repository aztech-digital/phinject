<?php

namespace Aztech\Phinject\Activators\Lazy;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\ServiceBuilder;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class LazyActivator implements Activator
{

    private $serviceBuilder;

    public function __construct(ServiceBuilder $serviceBuilder)
    {
        $this->serviceBuilder = $serviceBuilder;
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Activator::createInstance()
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        if (! $serviceConfig->resolve('lazy', false)) {
            return $this->serviceBuilder->buildService($container, $serviceConfig, $serviceName);
        }

        if ($serviceConfig->resolve('class', false) == false) {
            throw new UnbuildableServiceException($serviceName . ' : "class" keyword is mandatory for lazy objects (interface or class name).');
        }

        $serviceBuilder = $this->serviceBuilder;
        $factory = new LazyLoadingValueHolderFactory();
        $invoker = new LazyActivationInvoker($container, $serviceBuilder, $serviceConfig, $serviceName);

        return $factory->createProxy($serviceConfig->resolve('class'), $invoker->createClosure());
    }
}
