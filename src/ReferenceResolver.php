<?php

namespace Aztech\Phinject;

interface ReferenceResolver
{
    public function resolve($reference);

    public function resolveMany(array $references);
}
