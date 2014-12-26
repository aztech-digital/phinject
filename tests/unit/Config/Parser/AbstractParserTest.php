<?php

namespace Aztech\Phinject\Tests\Config\Parser;

abstract class AbstractParserTest extends \PHPUnit_Framework_TestCase
{

    protected function getData($name)
    {
        return file_get_contents(getcwd() . '/tests/res/config-parser-test-files/' . $name);
    }

    abstract protected function getParser();

    abstract protected function getParsableData();

    public function testUnparseReturnsParsableData()
    {
        $data = $this->getParsableData();
        $parser = $this->getParser();

        $config = $parser->parse($data);
        $data = $parser->unparse($config);
        $config = $parser->parse($data);

        $this->assertArrayHasKey('params', $config);
        $this->assertArrayHasKey('classes', $config);
    }
}
