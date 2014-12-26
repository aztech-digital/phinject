<?php

namespace Aztech\Phinject\Tests\Activators\Reflection;

use Aztech\Phinject\Activators\Reflection\InstanceInvocationActivator;
use Aztech\Phinject\Util\ArrayResolver;

class InstanceInvocationActivatorTest extends \PHPUnit_Framework_TestCase
{

    protected $service;

    protected $container;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new \stdClass();
    }

    public function stubFactoryMethod()
    {
        return $this->service;
    }

    /**
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testActicationFailsWithMissingMethod()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('provider'))
            ->will($this->returnValue($this));

        $activator = new InstanceInvocationActivator();

        $instance = $activator->createInstance($this->container,
            new ArrayResolver(array('builder' => 'provider->unknownFactoryMethod')), 'dependency');

        $this->assertSame($this->service, $instance);
    }

    public function testActivationSucceedsWithNoArgs()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('provider'))
            ->will($this->returnValue($this));

        $activator = new InstanceInvocationActivator();

        $instance = $activator->createInstance($this->container, new ArrayResolver([
                'builder' => 'provider->stubFactoryMethod'
        ]), 'dependency');

        $this->assertSame($this->service, $instance);
    }
}
