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

    public function __construct(ContainerInterface $container, Container $fallback = null)
    {
        $this->container = $container;
        $this->fallbackContainer = $fallback;
    }

    public function accepts($reference)
    {
        return substr($reference, 0, 1) == '@' || ! ($this->container instanceof Container);
    }

    public function resolve($reference)
    {
        $reference = substr($reference, 1);

        try {
            return $this->container->get($reference);
        }
        catch (NotFoundException $exception) {
            if ($this->fallbackContainer) {
                return $this->fallbackContainer->get($reference);
            }

            throw new UnknownDefinitionException($reference, $exception);
        }
    }
}
