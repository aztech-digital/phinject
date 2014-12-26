<?php

namespace Aztech\Phinject\Resolver;

class PassThroughResolver implements Resolver
{

    public function accepts($reference)
    {
        return true;
    }

    public function resolve($reference)
    {
        return $reference;
    }
}
