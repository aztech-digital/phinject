<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface ActivatorFactory
{

    /**
     *
     * @param string $key
     * @return void
     */
    function addActivator($key, Activator $activator);

    /**
     *
     * @param string $serviceName
     * @param ArrayResolver $configuration
     * @throws UnbuildableServiceException
     * @return Activator
     */
    function getActivator($serviceName, ArrayResolver $configuration);
}
