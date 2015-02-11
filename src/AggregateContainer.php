<?php

namespace Aztech\Phinject;

use Interop\Container\ContainerInterface;

class AggregateContainer implements ContainerInterface
{

    private $containers;

    public function addContainer(ContainerInterface $container)
    {
        $this->containers[] = $container;
    }

    /**
     * (non-PHPdoc)
     * @see \Interop\Container\ContainerInterface::get()
     */
    public function get($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        throw new UnknownDefinitionException($id);
    }

    /*
     * (non-PHPdoc)
     * @see \Interop\Container\ContainerInterface::has()
     */
    public function has($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }
}