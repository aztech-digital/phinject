<?php

namespace Aztech\Phinject\Activators\Lazy;

use Aztech\Phinject\Container;
use Aztech\Phinject\ServiceBuilder;
use Aztech\Phinject\Util\ArrayResolver;

class LazyActivationInvoker
{

    private $container;

    private $serviceBuilder;

    private $serviceConfig;

    private $serviceName;

    /**
     * @param string $serviceName
     */
    public function __construct(Container $container, ServiceBuilder $builder, ArrayResolver $serviceConfig, $serviceName)
    {
        $this->container = $container;
        $this->serviceBuilder = $builder;
        $this->serviceConfig = $serviceConfig;
        $this->serviceName  = $serviceName;
    }

    /**
     *
     * @param mixed $object
     * @param mixed $initializer
     * @return boolean
     */
    public function __invoke(& $object, & $initializer)
    {
        $object = $this->serviceBuilder->buildService(
            $this->container,
            $this->serviceConfig,
            $this->serviceName
        );

        $initializer = null;

        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function createClosure()
    {
        return function(& $object, $proxy, $method, $parameters, & $initializer) {
            return $this($object, $initializer);
        };
    }
}
