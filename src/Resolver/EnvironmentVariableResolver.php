<?php

namespace Aztech\Phinject\Resolver;

class EnvironmentVariableResolver extends RegexMatchingResolver
{

    public function resolve($reference)
    {
        return getenv($this->extractMatch($reference));
    }
}
