<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Injector;
use Aztech\Phinject\Util\ArrayResolver;
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
            $parameters = $callConfig->resolveArray($methodName, []);
            $method = $this->methodParser->parseInvocation($service, $methodName, $parameters);

            $this->methodInvoker->invoke($container, $service, $method);
        }

        return true;
    }
}
