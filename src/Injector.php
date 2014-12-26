<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface Injector
{

    /**
     * @return boolean
     */
    public function inject(Container $container, ArrayResolver $serviceConfig, $service);
}
