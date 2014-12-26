<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Config;

class TemplatedConfig extends AbstractConfig
{

    private $config;

    private $processor;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->processor = new TemplatedConfigProcessor();
    }

    protected function doLoad()
    {
        $config = $this->config->getResolver();
        $processed = $this->processor->process($config);

        return $processed->extract();
    }
}
