<?php

namespace Aztech\Phinject\Resolver;

class ConstantResolver extends RegexMatchingResolver
{

    public function resolve($reference)
    {
        return constant($this->extractMatch($reference));
    }
}
