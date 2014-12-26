<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Container;
use Aztech\Phinject\Injector;
use Aztech\Phinject\Util\ArrayResolver;

class ClassHierarchyMethodInjector implements Injector
{

    private $methodInvoker;

    public function __construct()
    {
        $this->methodInvoker = new MethodInvoker();
    }

    function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $config = $container->getGlobalConfig();

        foreach ($config->resolve('global.injections', array()) as $baseClassName => $calls)
        {
            if (! $this->isInjectionApplicableFor($service, $baseClassName)) {
                continue;
            }

            foreach($calls as $methodName => $parameters) {
                $this->methodInvoker->invoke($container, $service, $methodName, $parameters);
            }
        }

        return true;
    }

    private function isInjectionApplicableFor($service, $baseClassName)
    {
        return $service instanceof $baseClassName;
    }
}
