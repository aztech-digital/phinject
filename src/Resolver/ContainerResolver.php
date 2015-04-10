<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;

class ContainerResolver extends RegexMatchingResolver
{

    private $container;

    /**
     * @param Container $container
     * @param string $regex
     */
    public function __construct(Container $container, $regex)
    {
        parent::__construct($regex);
        $this->container = $container;
    }

    public function resolve($reference)
    {
        return $this->container;
    }
}
