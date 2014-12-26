<?php

namespace Aztech\Phinject\Config;

interface Parser
{

    /**
     * @param string $string
     */
    function parse($string);

    /**
     * @return string
     */
    function unparse(array $data);
}
