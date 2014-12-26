<?php

namespace Aztech\Phinject\Tests\Activators;


use Aztech\Phinject\Activators\DefaultActivatorFactory;
use Aztech\Phinject\Util\ArrayResolver;

class DefaultActivatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    private $factory;

    protected function setUp()
    {
        $this->factory = new DefaultActivatorFactory();
    }

    public function getInvalidServiceConfigurations()
    {
        return array(
            array(new ArrayResolver(array())),
            array(new ArrayResolver(array('class' => '\DummyClass', 'builder' => '\invalidBuilderDefinition')))
        );
    }

    /**
     * @dataProvider getInvalidServiceConfigurations
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testGetActivatorThrowsExceptionForInvalidConfigurations($serviceConfig)
    {
        $this->factory->getActivator('myService', $serviceConfig);
    }

    public function testGetActivatorWithStaticBuilderConfigReturnsStaticActivator()
    {
        $serviceConfig = new ArrayResolver(array('class' => '\DummyClass', 'builder' => '\DummyBuilder::dummyFactoryMethod'));

        $this->assertInstanceOf(
            '\Aztech\Phinject\Activators\Reflection\StaticInvocationActivator',
            $this->factory->getActivator('myService', $serviceConfig)
        );
    }

    public function testGetActivatorWithInstanceBuilderConfigReturnsInstanceActivator()
    {
        $serviceConfig = new ArrayResolver(array('class' => '\DummyClass', 'builder' => '@DummyBuilder->dummyFactoryMethod'));

        $this->assertInstanceOf(
            '\Aztech\Phinject\Activators\Reflection\InstanceInvocationActivator',
            $this->factory->getActivator('myService', $serviceConfig)
        );
    }

    public function testGetActivatorWithRemoteConfigReturnsRemoteActivator()
    {
        $serviceConfig = new ArrayResolver(array('class' => '\DummyClass', 'remote' => array()));

        $this->assertInstanceOf(
            '\Aztech\Phinject\Activators\Remote\RemoteActivator',
            $this->factory->getActivator('myService', $serviceConfig)
        );
    }
}
