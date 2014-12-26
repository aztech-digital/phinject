<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\ReferenceResolver;

class DynamicParameterResolver implements Resolver
{

    private $resolver;

    public function __construct(ReferenceResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function accepts($reference)
    {
        if (substr($reference, 0, 1) === '\\') {
            return true;
        }

        return false;
    }

    public function resolve($reference)
    {
        return substr($reference, 1);
    }
}
