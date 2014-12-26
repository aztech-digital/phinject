<?php

namespace Aztech\Phinject\Tests\Config\Parser;

use Aztech\Phinject\Config\Parser\PhpParser;

class PhpParserTest extends AbstractParserTest
{
    public function testParsePhp()
    {
        $php = <<<EOC
            return [ 'test' => 'test-value' ];
EOC;

        $parser = new PhpParser();

        $array = $parser->parse($php);

        $this->assertEquals('test-value', $array['test']);
    }

    protected function getParser()
    {
        return new PhpParser();
    }

    protected function getParsableData()
    {
        return $this->getData('config.php');
    }

}
