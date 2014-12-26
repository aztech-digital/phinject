<?php

namespace Aztech\Phinject\Activators\Reflection;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodInvocationDefinition;
use Aztech\Phinject\Util\MethodNameParser;

class InstanceInvocationActivator implements Activator
{

    private $methodNameParser;

    public function __construct()
    {
        $this->methodNameParser = new MethodNameParser();
    }

    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $method = $this->methodNameParser->getMethodInvocation($serviceConfig->resolve('builder'));
        $owner = $this->normalizeOwner($method);

        $invocationSite = $container->get($owner);
        $activationArgs = $method->extractArguments($container, $serviceConfig);

        if (! method_exists($invocationSite, $method->getName())) {
            throw new UnbuildableServiceException(
                sprintf("Instance '%s' (%s) has no '%s' method.", $method->getOwner(), get_class($invocationSite), $method->getName()));
        }

        return call_user_func_array(array($invocationSite, $method->getName()), $activationArgs);
    }

    private function normalizeOwner(MethodInvocationDefinition $method)
    {
        if (substr($method->getOwner(), 0, 1) == '@') {
            return substr($method->getOwner(), 1);
        }

        return $method->getOwner();
    }
}
