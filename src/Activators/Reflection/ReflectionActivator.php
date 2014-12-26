<?php

namespace Aztech\Phinject\Activators\Reflection;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;

class ReflectionActivator implements Activator
{

    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $className = $serviceConfig['class'];

        if (! class_exists($className)) {
            throw new UnbuildableServiceException(sprintf("Class '%s' not found.", $className));
        }

        $class = new \ReflectionClass($className);
        $activationArgs = array();

        if (isset($serviceConfig['arguments'])) {
            if (! is_array($serviceConfig['arguments'])) {
                $serviceConfig['arguments'] = [ $serviceConfig['arguments'] ];
            }

            $activationArgs = $container->resolveMany($serviceConfig['arguments']);
        }

        return $class->newInstanceArgs($activationArgs);
    }
}
