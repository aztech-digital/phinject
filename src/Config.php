<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface Config
{

    public function load();

    public function getData();

    /**
     * @return ArrayResolver
     */
    public function getResolver();
}
