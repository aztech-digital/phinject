<?php

namespace Aztech\Phinject\Tests\Activators\Lazy;

use Aztech\Phinject\Activators\Lazy\LazyActivator;
use Aztech\Phinject\Util\ArrayResolver;

class LazyActivatorTest extends \PHPUnit_Framework_TestCase
{

    private $container;

    private $serviceBuilder;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceBuilder = $this->getMockBuilder('\Aztech\Phinject\ServiceBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testLazyActivatorDelegatesToRealActivatorWhenLazyIsDisabledOrMissing()
    {
        $this->serviceBuilder->expects($this->exactly(2))
            ->method('buildService');

        $activator = new LazyActivator($this->serviceBuilder);

        $config = new ArrayResolver(array(''));
        $activator->createInstance($this->container, $config, 'service');

        $config = new ArrayResolver(array('lazy' => false));
        $activator->createInstance($this->container, $config, 'service');
    }

    public function testLazyActivatorDoesNotInvokeRealActivatorWhenLazyIsEnabled()
    {
        $config = new ArrayResolver(array('lazy' => true, 'class' => '\stdClass'));

        $activator = new LazyActivator($this->serviceBuilder);
        $instance = $activator->createInstance($this->container, $config, 'service');
    }

    public function testRealActivatorisInvokedWhenLazyObjectIsUsed()
    {
        $config = new ArrayResolver(array('lazy' => true, 'class' => '\Aztech\Phinject\Tests\Activators\Lazy\LazyActivatorTestClass'));

        $activator = new LazyActivator($this->serviceBuilder);
        $instance = $activator->createInstance($this->container, $config, 'service');

        $this->serviceBuilder->expects($this->atLeastOnce())
            ->method('buildService')
            ->willReturn(new \stdClass());

        $instance->hello;
    }
}

class LazyActivatorTestClass
{
    public $hello;
}
