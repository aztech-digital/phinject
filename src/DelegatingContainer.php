<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;

interface DelegatingContainer
{
    function setDelegateContainer(ContainerInterface $container);
}
