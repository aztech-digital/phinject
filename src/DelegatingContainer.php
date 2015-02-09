<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;

interface DelegatingContainer extends Container
{
    function setDelegateContainer(ContainerInterface $container);
}
