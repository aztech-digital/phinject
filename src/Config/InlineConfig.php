<?php

namespace Aztech\Phinject\Config;

class InlineConfig extends AbstractConfig
{

    private $parser;

    private $content;

    public function __construct(Parser $parser, $configText)
    {
        $this->parser = $parser;
        $this->content = $configText;
    }

    protected function doLoad()
    {
        return $this->parser->parse($this->content);
    }
}
