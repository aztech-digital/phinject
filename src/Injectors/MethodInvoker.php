<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodInvocationDefinition;

class MethodInvoker
{

    public function invokeStatic(Container $container, MethodInvocationDefinition $method)
    {
        if (! $method->isStatic()) {
            throw new \RuntimeException('Cannot invoke non-static method in static context.');
        }

        $args = $method->extractArguments($container, new ArrayResolver());

        $this->invokeMethod($method->getOwner(), $method->getName(), $args);
    }

    /**
     *
     * @param Container $container
     */
    public function invoke(Container $container, $service, MethodInvocationDefinition $method)
    {
        if (! is_object($service)) {
            throw new \InvalidArgumentException('Service must be an object.');
        }

        $args = $method->extractArguments($container, new ArrayResolver());

        $this->invokeMethod($service, $method->getName(), $args);
    }

    private function extractMethodNameFromList($methodName)
    {
        $matches = array();

        if (preg_match('`^([^\[]*)\[[0-9]*\]$`i', $methodName, $matches)) {
            return $matches[1];
        }

        throw new \RuntimeException(sprintf("Invalid method name '%s'", $methodName));
    }

    private function invokeMethod($service, $methodName, $parameters)
    {
        $methodToCall = $methodName;

        if ($this->isInInvocationList($methodName)) {
            $methodToCall = $this->extractMethodNameFromList($methodName);
        }

        if (! method_exists($service, $methodToCall)) {
            throw new \RuntimeException('Method "' . $methodToCall . '" not found in type "' . get_class($service) . '".');
        }

        call_user_func_array(array(
            $service,
            $methodToCall
        ), $parameters);
    }

    private function isInInvocationList($methodName)
    {
        return (false !== strpos($methodName, '['));
    }
}
