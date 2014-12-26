<?php

namespace Aztech\Phinject\Config\Parser;

use Aztech\Phinject\Config\Parser;

class PhpParser implements Parser
{

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Config\Parser::parse()
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function parse($string)
    {
        if (substr($string, 0, 5) == '<?php') {
            $string = substr($string, 5);
        }

        return eval($string);
    }

    public function unparse(array $data)
    {
        return 'return ' . var_export($data, true) . ';';
    }
}
