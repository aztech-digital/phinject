<?php

namespace Aztech\Phinject\Activators\Reflection;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodInvocationDefinition;
use Aztech\Phinject\Util\MethodNameParser;

class StaticInvocationActivator implements Activator
{

    private $methodNameParser;

    public function __construct()
    {
        $this->methodNameParser = new MethodNameParser();
    }

    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $method = $this->methodNameParser->getMethodInvocation($serviceConfig['builder']);

        $this->validate($method);

        $activationArgs = $method->extractArguments($container, $serviceConfig);
        $callback = array(
            $method->getOwner(),
            $method->getName()
        );

        return call_user_func_array($callback, $activationArgs);
    }

    /**
     * @param MethodInvocationDefinition $method
     */
    private function validate(MethodInvocationDefinition $method)
    {
        $this->validateClass($method);
        $this->validateMethod($method);
    }

    /**
     * @param MethodInvocationDefinition $method
     */
    private function validateClass($method)
    {
        if (! class_exists($method->getOwner())) {
            throw new UnbuildableServiceException(sprintf("Class '%s' not found.", $method->getOwner()));
        }
    }

    /**
     * @param MethodInvocationDefinition $method
     */
    private function validateMethod($method)
    {
        if (! method_exists($method->getOwner(), $method->getName())) {
            throw new UnbuildableServiceException(sprintf("Class '%s' has no '%s' method.", $method->getOwner(), $method->getName()));
        }
    }
}
