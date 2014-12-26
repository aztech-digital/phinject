<?php

namespace Aztech\Phinject\Config;

class ArrayConfig extends AbstractConfig
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function doLoad()
    {
        return $this->config;
    }
}
