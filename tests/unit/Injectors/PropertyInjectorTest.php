<?php

namespace Aztech\Phinject\Tests\Injectors;

use Aztech\Phinject\Injectors\PropertyInjector;
use Aztech\Phinject\Util\ArrayResolver;

class PropertyInjectorTest extends \PHPUnit_Framework_TestCase
{
    public function testDefinedMethodsAreInvoked()
    {
        $mock = new \stdClass();

        $container = $this->getMock('\Aztech\Phinject\Container');

        $container->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue(2));

        $injector = new PropertyInjector();
        $serviceConfig = new ArrayResolver(array('props' => array('property' => array('value'))));

        $injector->inject($container, $serviceConfig, $mock);

        $this->equalTo(2, $mock->property);
    }
}
