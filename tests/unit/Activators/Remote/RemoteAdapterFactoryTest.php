<?php

namespace Aztech\Phinject\Tests\Activators\Remote;

use Aztech\Phinject\Activators\Remote\RemoteAdapterFactory;
use Aztech\Phinject\Util\ArrayResolver;

class RemoteAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function getInvalidConfigurations()
    {
        return array(
            array(array()),
            array(array('protocol' => 'weird')),
            array(array('endpoint' => 'uri'))
        );
    }

    /**
     * @dataProvider getInvalidConfigurations
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfigurationThrowsException($serviceConfig)
    {
        $factory = new RemoteAdapterFactory();

        $factory->getAdapter('myService', new ArrayResolver($serviceConfig));
    }

    public function getExpectedClassNamesByProtocol()
    {
        return array(
            array('\ProxyManager\Factory\RemoteObject\Adapter\XmlRpc', array('protocol' => 'xml-rpc', 'endpoint' => 'localhost')),
            array('\ProxyManager\Factory\RemoteObject\Adapter\JsonRpc', array('protocol' => 'json-rpc', 'endpoint' => 'localhost')),
            array('\ProxyManager\Factory\RemoteObject\Adapter\Soap', array('protocol' => 'soap', 'endpoint' => 'localhost')),
            array('\Aztech\Phinject\Activators\Remote\Rest\RestAdapter', array('protocol' => 'rest', 'endpoint' => 'localhost'))
        );
    }

    /**
     * @dataProvider getExpectedClassNamesByProtocol
     */
    public function testGetAdapterReturnsProperInstanceBasedOnProtocol($expectedClass, $serviceConfig)
    {
        $factory = new RemoteAdapterFactory();

        $adapter = $factory->getAdapter('myService', new ArrayResolver($serviceConfig));

        $this->assertInstanceOf('\ProxyManager\Factory\RemoteObject\AdapterInterface', $adapter);
        $this->assertInstanceOf($expectedClass, $adapter);
    }

    /**
     * @expectedException \Aztech\Phinject\Activators\Remote\UnknownProtocolException
     */
    public function testGetAdapterThrowsExceptionForUnknownProtocol()
    {
        $serviceConfig = array('protocol' => 'unknown-protocol', 'endpoint' => 'localhost');
        $factory = new RemoteAdapterFactory();

        $adapter = $factory->getAdapter('myService', new ArrayResolver($serviceConfig));
    }
}
