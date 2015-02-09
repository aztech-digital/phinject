<?php

namespace Aztech\Phinject\Tests;

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

    public function testGetFallsbackToLocalLookupWhenDelegationFails()
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

        $lookup = $container->get('lookup');

        $this->assertNotNull($lookup->test);
        $this->assertInstanceOf('\stdClass', $lookup->test);
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
}