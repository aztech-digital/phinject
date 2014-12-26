<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Config;

class AliasedConfig extends AbstractConfig
{

    private $config;

    private $processor;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->processor = new AliasedConfigProcessor();
    }

    protected function doLoad()
    {
        $config = $this->config->getResolver();
        $processed = $this->processor->process($config);

        return $processed->extract();
    }
}
