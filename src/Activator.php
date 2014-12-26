<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface Activator
{
    /**
     *
     * @param Container $container
     * @param ArrayResolver $serviceConfig
     * @param string $serviceName
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName);
}
