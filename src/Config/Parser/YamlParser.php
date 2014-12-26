<?php

namespace Aztech\Phinject\Config\Parser;

use Aztech\Phinject\Config\Parser;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements Parser
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Yaml();
    }

    public function parse($string)
    {
        return $this->parser->parse($string);
    }

    public function unparse(array $data)
    {
        return $this->parser->dump($data);
    }
}
