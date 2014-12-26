<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Injector;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodInvocationDefinition;
use Aztech\Phinject\Util\MethodNameParser;

class MethodInjector implements Injector
{

    private $methodInvoker;

    private $methodParser;

    public function __construct()
    {
        $this->methodInvoker = new MethodInvoker();
        $this->methodParser = new MethodNameParser();
    }

    public function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $callConfig = $serviceConfig->resolve('call', []);

        foreach($callConfig->extractKeys() as $methodName) {
            $parameters = $callConfig->resolve($methodName, [], true);
            $method = $this->getInvocation($service, $methodName, $parameters);

            $this->methodInvoker->invoke($container, $service, $method);
        }

        return true;
    }

    private function getInvocation($service, $methodName, $parameters) {
        if (is_int($methodName)) {
            return $this->methodParser->getFunctionInvocation($parameters[0]);
        }

        return new MethodInvocationDefinition($service, $methodName, false, $parameters->extract());
    }
}
