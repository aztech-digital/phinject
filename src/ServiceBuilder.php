<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Initializer\OnceTypeInitializer;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\Util\ArrayResolver;

interface ServiceBuilder
{

    /**
     * Chain of command of the class loader
     *
     * @param  ArrayResolver $serviceConfig
     * @param string $serviceName
     * @return object
     */
    public function buildService(Container $container, ArrayResolver $serviceConfig, $serviceName);
}
