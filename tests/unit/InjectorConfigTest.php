<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;
use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\ConfigurationAware;
use Aztech\Phinject\Injector;

class InjectorConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testCustomActivatorAreBoundViaConfigSection()
    {
        $config = <<<YML
config:
    injectors:
        dummy:
            class: \Aztech\Phinject\Tests\DummyInjector
            key: dummy-inject

classes:
    dummyObject:
        class: \stdClass
        dummy-inject: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertTrue($object->isInjected);
    }

    public function testKeyDefaultsToInjectorKey()
    {
        $config = <<<YML
config:
    injectors:
        dummy:
            class: \Aztech\Phinject\Tests\DummyInjector

classes:
    dummyObject:
        class: \stdClass
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertTrue($object->isInjected);
    }

    public function testScalarValueIsConvertedToArrayConfig()
    {
        $config = <<<YML
config:
    injectors:
        dummy: \Aztech\Phinject\Tests\DummyInjector

classes:
    dummyObject:
        class: \stdClass
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertTrue($object->isInjected);
    }

    public function testConfigIsInjectedInConfigurationAwareInjectors()
    {
        $config = <<<YML
config:
    injectors:
        dummy: \Aztech\Phinject\Tests\ConfigAwareDummyInjector

classes:
    dummyObject:
        class: \stdClass
        dummy: testDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($config);

        $object = $container->get('dummyObject');

        $this->assertInstanceOf('\stdClass', $object);
        $this->assertTrue($object->hasConfig);
    }
}

class DummyInjector implements Injector
{
    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Phinject\Injector::inject()
     */
    public function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $service->isInjected = true;
    }
}

class ConfigAwareDummyInjector implements Injector, ConfigurationAware
{

    private $configurationSet = false;

    public function setConfiguration(ArrayResolver $configurationNode)
    {
        $this->configurationSet = true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Phinject\Injector::inject()
     */
    public function inject(Container $container, ArrayResolver $serviceConfig, $service)
    {
        $service->hasConfig = $this->configurationSet;
    }
}