<?php

namespace Aztech\Phinject\Resolver;

interface Resolver
{

    function accepts($reference);

    function resolve($reference);
}
