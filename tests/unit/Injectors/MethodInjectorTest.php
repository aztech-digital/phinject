<?php
namespace Aztech\Phinject\Tests\Injectors;

use Aztech\Phinject\Injectors\MethodInjector;
use Aztech\Phinject\Util\ArrayResolver;

class MethodInjectorTest extends \PHPUnit_Framework_TestCase
{

    public function testDefinedMethodsAreInvoked()
    {
        $mock = $this->getMock('\Aztech\Phinject\Tests\Injectors\TestInjectable');

        $mock->expects($this->once())
            ->method('setMethod')
            ->with($this->equalTo(2));

        $container = $this->getMock('\Aztech\Phinject\Container');

        $container->expects($this->once())
            ->method('resolveMany')
            ->will($this->returnValue(array(2)));

        $injector = new MethodInjector();

        $serviceConfig = new ArrayResolver(array('call' => array('setMethod' => array('value'))));

        $actual = $injector->inject($container, $serviceConfig, $mock);
    }

    public function testArrayOfParameterArraysInvokesInjectionMultipleTimes()
    {
        $mock = $this->getMock('\Aztech\Phinject\Tests\Injectors\TestInjectable');

        $mock->expects($this->exactly(2))
            ->method('setMethod')
            ->with($this->equalTo(2));

        $container = $this->getMock('\Aztech\Phinject\Container');

        $container->expects($this->any())
            ->method('resolveMany')
            ->will($this->returnValue(array(2)));

        $injector = new MethodInjector();

        $serviceConfig = new ArrayResolver(array('call' => array('setMethod[0]' => array('value'), 'setMethod[1]' => array('value2'))));

        $actual = $injector->inject($container, $serviceConfig, $mock);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMalformedMethodNameThrowsRuntime()
    {
        $mock = $this->getMock('\Aztech\Phinject\Tests\Injectors\TestInjectable');
        $container = $this->getMock('\Aztech\Phinject\Container');

        $injector = new MethodInjector();

        $serviceConfig = new ArrayResolver(array('call' => array('setMethod[abc' => array('value'))));

        $actual = $injector->inject($container, $serviceConfig, $mock);
    }

}

class TestInjectable
{

    public function setMethod($value)
    {}
}
