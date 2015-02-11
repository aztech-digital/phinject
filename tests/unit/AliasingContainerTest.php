<?php

namespace Aztech\Phinject;

class AliasingContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testAliasedDefinitionsResolveCorrectObject()
    {
        $yaml = <<<YML
classes:
    original:
        class: \stdClass
        properties:
            stub: stub
    aliased:
        alias: @original
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml, [ 'aliases' => true ]);

        $this->assertTrue($container->has('aliased'));

        $aliased = $container->get('aliased');
        $original = $container->get('original');

        $this->assertSame($original, $aliased);
    }

    public function testAliasedDefinitionsAreEnabledViaConfiguration()
    {
        $yaml = <<<YML
configuration:
    aliases: true
classes:
    original:
        class: \stdClass
        properties:
            stub: stub
    aliased:
        alias: @original
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $this->assertTrue($container->has('aliased'));

        $aliased = $container->get('aliased');
        $original = $container->get('original');

        $this->assertSame($original, $aliased);
    }
}