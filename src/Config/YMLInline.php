<?php

namespace Aztech\Phinject\Config;

use Symfony\Component\Yaml\Yaml;

class YMLInline extends AbstractConfig
{

    protected $parser;

    protected $inline = '';

    protected $data = array();

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->inline = $string;
        $this->parser = new Yaml();
    }

    protected function doLoad()
    {
        $data = $this->parser->parse($this->inline);

        if (! is_array($data)) {
            $data = [ $data ];
        }

        return $data;
    }

    public function compile()
    {
        return var_export($this->load(), true);
    }
}
