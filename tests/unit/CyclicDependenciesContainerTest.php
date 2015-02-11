<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;
use Aztech\Phinject\Config\ArrayConfig;

class CyclicDependenciesContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testCircularDependenciesViaLazyObjects()
    {
        $config = <<<EOC
classes:
    depA:
        class: Aztech\Phinject\Tests\CircularDependency
        arguments: [ '@depB', 1 ]
        lazy: true
        singleton: true
    depB:
        class: Aztech\Phinject\Tests\CircularDependency
        arguments: [ '@depA', 2 ]
        lazy: true
        singleton: true
EOC;

        $container = ContainerFactory::createFromInlineYaml($config, [ 'deferred' => true ]);

        // Required to get hold of the initialized proxies
        $b = $container->get('depA')->getCircular();
        $a = $container->get('depB')->getCircular();

        $this->assertEquals(1, $a->getValue());
        $this->assertEquals(2, $b->getValue());

        $this->assertEquals($a->getCircular(), $b);
        $this->assertEquals($b->getCircular(), $a);
    }

    public function testCyclicDependenciesDoNotOverflowWithOneSingletonInCycleA()
    {
        $yaml = <<<YML
configuration:
    deferred: false
classes:
    cyclic:
        class: \stdClass
        singleton: true
        properties:
            cyclic: "@inverted"
    inverted:
        class: \stdClass
        singleton: false
        properties:
            cyclic: "@cyclic"
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $cyclic = $container->get('cyclic');
        $inverted = $container->get('inverted');

        $this->assertSame($cyclic, $inverted->cyclic);
        $this->assertNotSame($inverted, $cyclic->cyclic);
    }

    public function testCyclicDependenciesDoNotOverflowWithOneSingletonInCycleB()
    {
        $yaml = <<<YML
configuration:
    deferred: false
classes:
    cyclic:
        class: \stdClass
        singleton: false
        properties:
            cyclic: "@inverted"
    inverted:
        class: \stdClass
        singleton: true
        properties:
            cyclic: "@cyclic"
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $cyclic = $container->get('cyclic');
        $inverted = $container->get('inverted');

        $this->assertNotSame($cyclic, $inverted->cyclic);
        $this->assertSame($inverted, $cyclic->cyclic);
    }

    public function testCyclicDependenciesDoNotOverflowWithTwoSingletonsInCycle()
    {
        $yaml = <<<YML
configuration:
    deferred: false
classes:
    cyclic:
        class: \stdClass
        singleton: true
        properties:
            cyclic: "@inverted"
    inverted:
        class: \stdClass
        singleton: true
        properties:
            cyclic: "@cyclic"
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $cyclic = $container->get('cyclic');
        $inverted = $container->get('inverted');

        $this->assertSame($cyclic, $inverted->cyclic);
        $this->assertSame($inverted, $cyclic->cyclic);
    }
}

class CircularDependency
{
    private $circular;

    private $value;

    public function __construct(self $other, $value)
    {
        $this->circular = $other;
        $this->value = $value;
    }

    public function getCircular()
    {
        return $this->circular;
    }

    public function getValue()
    {
        return $this->value;
    }
}