<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;
use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\ConfigurationAware;

class ActivatorConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testCustomActivatorAreBoundViaConfigSection()
    {
        $config = <<<YML
config:
    activators:
        dummy:
            class: \Aztech\Phinject\Tests\DummyActivator
            key: dummy-activate

classes:
    dummyObject:
        dummy-activate: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertInstanceOf('\stdClass', $object);
    }

    public function testKeyDefaultsToActivatorKey()
    {
        $config = <<<YML
config:
    activators:
        dummy:
            class: \Aztech\Phinject\Tests\DummyActivator

classes:
    dummyObject:
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertInstanceOf('\stdClass', $object);
    }

    public function testScalarValueIsConvertedToArrayConfig()
    {
        $config = <<<YML
config:
    activators:
        dummy: \Aztech\Phinject\Tests\DummyActivator

classes:
    dummyObject:
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertInstanceOf('\stdClass', $object);
    }

    public function testConfigIsInjectedInConfigurationAwareActivators()
    {
        $config = <<<YML
config:
    activators:
        dummy: \Aztech\Phinject\Tests\ConfigAwareDummyActivator

classes:
    dummyObject:
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertInstanceOf('\stdClass', $object);
        $this->assertTrue($object->hasConfig);
    }
}

class DummyActivator implements Activator
{
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        return new \stdClass();
    }
}

class ConfigAwareDummyActivator implements Activator, ConfigurationAware
{
    private $configurationSet = false;

    public function setConfiguration(ArrayResolver $configurationNode)
    {
        $this->configurationSet = true;
    }

    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        $item = new \stdClass();
        $item->hasConfig = $this->configurationSet;

        return $item;
    }
}