<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\AggregateContainer;
use Aztech\Phinject\ContainerFactory;
use Aztech\Phinject\UnknownDefinitionException;

class DelegateContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetReturnsLocalEntriesButDelegatesDependencies()
    {
        $yml = <<<YML
classes:
    lookup:
        class: \stdClass
        properties:
            test: @dependency
YML;

        $delegateContainer = $this->prophesize('\Interop\Container\ContainerInterface');
        $delegateContainer->get('dependency')->willReturn(new \stdClass())->shouldBeCalled();

        $container = ContainerFactory::createFromInlineYaml($yml);
        $container->setDelegateContainer($delegateContainer->reveal());

        $container->get('lookup');
    }

    /**
     * @expectedException Aztech\Phinject\UnknownDefinitionException
     */
    public function testGetDoesNotFallbackToLocalLookupWhenDelegationFails()
    {
        $yml = <<<YML
classes:
    dependency:
        class: \stdClass
        properties:
            test: 'hello'
    lookup:
        class: \stdClass
        properties:
            test: @dependency
YML;

        $delegateContainer = $this->prophesize('\Interop\Container\ContainerInterface');
        $delegateContainer->get('dependency')->willThrow(new UnknownDefinitionException('dependency'));

        $container = ContainerFactory::createFromInlineYaml($yml);
        $container->setDelegateContainer($delegateContainer->reveal());

        $container->get('lookup');
    }

    public function testHasReturnsLocalLookupResultAndDoesNotDelegate()
    {
        $yml = <<<YML
classes:
    lookup:
        class: \stdClass
        properties:
            test: @dependency
YML;

        $delegateContainer = $this->prophesize('\Interop\Container\ContainerInterface');
        $delegateContainer->has('missing')->shouldNotBeCalled();

        $container = ContainerFactory::createFromInlineYaml($yml);
        $container->setDelegateContainer($delegateContainer->reveal());

        $this->assertTrue($container->has('lookup'));
        $this->assertFalse($container->has('missing'));
    }

    public function testDelegateLookupWithAggregateRootSucceeds()
    {
        $ymlA = <<<YML
classes:
    lookup:
        class: \stdClass
        properties:
            test: @dependency
YML;

        $ymlB = <<<YML
classes:
    dependency:
        class: \stdClass
        properties:
            property: "value"
YML;

        $containerA = ContainerFactory::createFromInlineYaml($ymlA);
        $containerB = ContainerFactory::createFromInlineYaml($ymlB);

        $aggregateRoot = new AggregateContainer();
        $containerA->setDelegateContainer($aggregateRoot);

        $aggregateRoot->addContainer($containerA);
        $aggregateRoot->addContainer($containerB);

        $this->assertTrue($aggregateRoot->has('lookup'));
        $this->assertTrue($aggregateRoot->has('dependency'));

        $lookup = $aggregateRoot->get('lookup');
        $dependency = $aggregateRoot->get('dependency');

        $this->assertInstanceOf('\stdClass', $lookup);
        $this->assertInstanceOf('\stdClass', $dependency);

        $this->assertSame($dependency, $lookup->test);
        $this->assertEquals('value', $dependency->property);
    }

    public function testDelegateLookupWithAggregateLoopsBackToContainer()
    {
        $ymlA = <<<YML
classes:
    lookup:
        class: \stdClass
        properties:
            test: @dependencyLocal
    dependencyLocal:
        class: \stdClass
        properties:
            property: "local"
YML;

        $containerA = ContainerFactory::createFromInlineYaml($ymlA);
        $containerB = $this->prophesize('\Interop\Container\ContainerInterface');

        $containerB->has('dependencyLocal')->willReturn(false)->shouldBeCalled();
        $containerB->has('lookup')->willReturn(false)->shouldBeCalled();

        $aggregateRoot = new AggregateContainer();
        $containerA->setDelegateContainer($aggregateRoot);

        $aggregateRoot->addContainer($containerB->reveal());
        $aggregateRoot->addContainer($containerA);

        $this->assertTrue($aggregateRoot->has('lookup'));
        $this->assertTrue($aggregateRoot->has('dependencyLocal'));

        $lookup = $aggregateRoot->get('lookup');
        $dependency = $aggregateRoot->get('dependencyLocal');

        $this->assertInstanceOf('\stdClass', $lookup);
        $this->assertInstanceOf('\stdClass', $dependency);

        $this->assertSame($dependency, $lookup->test);
        $this->assertEquals('local', $dependency->property);
    }
}