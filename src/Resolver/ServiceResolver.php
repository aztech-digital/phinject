<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;
use Aztech\Phinject\UnknownDefinitionException;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\NotFoundException;

class ServiceResolver implements Resolver
{

    private $container;

    private $fallbackContainer;

    private $firstResolveCall = true;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function accepts($reference)
    {
        return substr($reference, 0, 1) == '@';
    }

    public function resolve($reference)
    {
        $reference = substr($reference, 1);

        return $this->container->get($reference);
    }
}
