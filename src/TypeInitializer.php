<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface TypeInitializer
{
    /**
     * @return void
     */
    function initialize(Container $container, ArrayResolver $serviceConfig);
}
