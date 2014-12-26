<?php

namespace Aztech\Phinject\Tests\Activators\Reflection;

use Aztech\Phinject\Activators\Reflection\ReflectionActivator;
use Aztech\Phinject\Util\ArrayResolver;

class ReflectionActivatorTest extends \PHPUnit_Framework_TestCase
{

    protected $container;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testActicationFailsWithMissingClass()
    {
        $activator = new ReflectionActivator();

        $instance = $activator->createInstance($this->container, new ArrayResolver(array(
            'class' => '\Aztech\Phinject\UnpossiblyFindableClass'
        )), 'dependency');
    }

    public function testActivationSucceedsWithNoArgs()
    {
        $activator = new ReflectionActivator();

        $instance = $activator->createInstance($this->container, new ArrayResolver(array(
            'class' => '\stdClass'
        )), 'dependency');

        $this->assertNotNull($instance);
        $this->assertInstanceOf('\stdClass', $instance);
    }

    public function testActivationSucceedsWithArgs()
    {
        $this->container->expects($this->any())
            ->method('resolveMany')
            ->will($this->returnArgument(0));

        $activator = new ReflectionActivator();

        $instance = $activator->createInstance($this->container, new ArrayResolver(array(
            'class' => '\Aztech\Phinject\Tests\Activators\Reflection\TestConstructorInjectable',
            'arguments' => array(
                2
            )
        )), 'dependency');

        $this->assertNotNull($instance);
        $this->assertInstanceOf('\Aztech\Phinject\Tests\Activators\Reflection\TestConstructorInjectable', $instance);
    }
}

class TestConstructorInjectable
{

    public $constructorInjectedValue = 0;

    public function __construct($value)
    {
        $this->constructorInjectedValue = $value;
    }
}
