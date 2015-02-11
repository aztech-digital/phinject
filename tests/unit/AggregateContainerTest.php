<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\AggregateContainer;
use Prophecy\Argument;

class AggregateContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testHasReturnsTrueWhenAtLeastOneChildContainerReturnsTrue()
    {
        $childA = $this->prophesize('\Interop\Container\ContainerInterface');
        $childB = $this->prophesize('\Interop\Container\ContainerInterface');
        $childC = $this->prophesize('\Interop\Container\ContainerInterface');

        $childA->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childB->has(Argument::any())->willReturn(true)->shouldBeCalled();
        $childC->has(Argument::any())->willReturn(false)->shouldNotBeCalled();

        $container = new AggregateContainer();

        $container->addContainer($childA->reveal());
        $container->addContainer($childB->reveal());
        $container->addContainer($childC->reveal());

        $this->assertTrue($container->has('object'));
    }

    public function testHasReturnsFalseWhenAllChildContainersReturnFalse()
    {
        $childA = $this->prophesize('\Interop\Container\ContainerInterface');
        $childB = $this->prophesize('\Interop\Container\ContainerInterface');
        $childC = $this->prophesize('\Interop\Container\ContainerInterface');

        $childA->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childB->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childC->has(Argument::any())->willReturn(false)->shouldBeCalled();

        $container = new AggregateContainer();

        $container->addContainer($childA->reveal());
        $container->addContainer($childB->reveal());
        $container->addContainer($childC->reveal());

        $this->assertFalse($container->has('object'));
    }

    public function testGetReturnsValueFromChildWhenChildHasRequestedObject()
    {
        $childA = $this->prophesize('\Interop\Container\ContainerInterface');
        $childB = $this->prophesize('\Interop\Container\ContainerInterface');
        $childC = $this->prophesize('\Interop\Container\ContainerInterface');

        $object = new \stdClass();

        $childA->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childB->has(Argument::any())->willReturn(true)->shouldBeCalled();
        $childC->has(Argument::any())->willReturn(false)->shouldNotBeCalled();

        $childB->get('object')->willReturn($object)->shouldBeCalled();

        $container = new AggregateContainer();

        $container->addContainer($childA->reveal());
        $container->addContainer($childB->reveal());
        $container->addContainer($childC->reveal());

        $this->assertSame($object, $container->get('object'));
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testGetThrowsExceptionWhenNoChildHasRequestedObject()
    {
        $childA = $this->prophesize('\Interop\Container\ContainerInterface');
        $childB = $this->prophesize('\Interop\Container\ContainerInterface');
        $childC = $this->prophesize('\Interop\Container\ContainerInterface');

        $object = new \stdClass();

        $childA->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childB->has(Argument::any())->willReturn(false)->shouldBeCalled();
        $childC->has(Argument::any())->willReturn(false)->shouldBeCalled();

        $childB->get('object')->willReturn($object)->shouldNotBeCalled();

        $container = new AggregateContainer();

        $container->addContainer($childA->reveal());
        $container->addContainer($childB->reveal());
        $container->addContainer($childC->reveal());

        $container->get('object');
    }
}