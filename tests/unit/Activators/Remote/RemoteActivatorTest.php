<?php

namespace Aztech\Phinject\Tests\Activators\Remote;

use Aztech\Phinject\Activators\Remote\RemoteActivator;
use Aztech\Phinject\Util\ArrayResolver;

class RemoteActivatorTest extends \PHPUnit_Framework_TestCase
{

    public function getInvalidConfigurations()
    {
        return array(
            array(array()),
            array(array('class' => '\DummyClass')),
            array(array('remote' => array()))
        );
    }

    /**
     * @dataProvider getInvalidConfigurations
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testInvalidConfigurationThrowsException($serviceConfig)
    {
        $container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactory = $this->getMockBuilder('\Aztech\Phinject\Activators\Remote\RemoteAdapterFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $activator = new RemoteActivator($adapterFactory);

        $activator->createInstance($container, new ArrayResolver($serviceConfig), 'myService');
    }

    public function testValidConfigurationReturnsInstanceOfRequestedType()
    {
        $container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactory = $this->getMockBuilder('\Aztech\Phinject\Activators\Remote\RemoteAdapterFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceConfig = array(
            'class' => '\stdClass',
            'remote' => array('endpoint' => 'http://localhost:80', 'protocol' => 'rest')
        );

        $adapterFactory->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->getMock('\ProxyManager\Factory\RemoteObject\AdapterInterface')));

        $activator = new RemoteActivator($adapterFactory);

        $instance = $activator->createInstance($container, new ArrayResolver($serviceConfig), 'myService');

        $this->assertInstanceOf('\stdClass', $instance);
    }
}
