<?php

namespace Aztech\Phinject\Tests\Config\Parser;

use Aztech\Phinject\Config\Parser\JsonParser;
use Aztech\Phinject\Util\ArrayResolver;
class JsonParserTest extends AbstractParserTest
{

    public function testParse()
    {
      $data = $this->getData('config.json');
      $parser = new JsonParser();

      $config = $parser->parse($data);

      $this->assertArrayHasKey('params', $config);
      $this->assertArrayHasKey('classes', $config);
    }

    /**
     * @expectedException \Exception
     */
    public function testParseThrowsExceptionOnInvalidData()
    {
        $data = '{\/}';
        $parser = new JsonParser();

        $config = $parser->parse($data);
    }

    protected function getParsableData()
    {
        return $this->getData('config.json');
    }

    protected function getParser()
    {
        return new JsonParser();
    }
}
