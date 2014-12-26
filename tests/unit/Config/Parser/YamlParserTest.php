<?php

namespace Aztech\Phinject\Tests\Config\Parser;

use Aztech\Phinject\Config\Parser\YamlParser;

class YamlParserTest extends AbstractParserTest
{
    public function testParseYaml()
    {
        $yaml = <<<EOC
test: test-value
EOC;

        $parser = new YamlParser();
        $array = $parser->parse($yaml);

        $this->assertEquals('test-value', $array['test']);
    }

    protected function getParser()
    {
        return new YamlParser();
    }

    protected function getParsableData()
    {
        return $this->getData('config.yml');
    }

}
