<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;

interface ConfigurationAware
{
    public function setConfiguration(ArrayResolver $configurationNode);
}
