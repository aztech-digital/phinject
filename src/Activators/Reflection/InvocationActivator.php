<?php

namespace Aztech\Phinject\Activators\Reflection;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodNameParser;

class InvocationActivator implements Activator
{
    /**
     *
     * @var Activator
     */
    private $instanceInvocationActivator;

    /**
     *
     * @var Activator
     */
    private $staticInvocationActivator;

    /**
     *
     * @var MethodNameParser
     */
    private $methodNameParser;

    public function __construct()
    {
        $this->instanceInvocationActivator = new InstanceInvocationActivator();
        $this->staticInvocationActivator = new StaticInvocationActivator();

        $this->methodNameParser = new MethodNameParser();
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Activator::createInstance()
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $activator = $this->getActivator($serviceName, $serviceConfig->resolveStrict('builder'));

        return $activator->createInstance($container, $serviceConfig, $serviceName);
    }

    private function getActivator($serviceName, $builderKey)
    {
        if ($this->methodNameParser->isStaticInvocation($builderKey)) {
            return $this->staticInvocationActivator;
        }

        if ($this->methodNameParser->isInstanceInvocation($builderKey)) {
            return $this->instanceInvocationActivator;
        }

        throw new UnbuildableServiceException(sprintf("Unbuildable service : '%s', no suitable activator found.", $serviceName));
    }
}
