<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;
use Prophecy\Argument;

class ContainerFactoryTest extends \PHPUnit_Framework_TestCase
{

    private $baseDir;

    protected function setUp()
    {
        $this->baseDir = __DIR__ . '/../res/config-factory-test-files';
    }

    public function testCreateFromJson()
    {
        $file = $this->baseDir . '/config.json';

        $container = ContainerFactory::createFromJson($file);

        $this->assertInstanceOf('\Aztech\Phinject\Container', $container);
    }


    public function testCreateFromPhp()
    {
        $file = $this->baseDir . '/config.php';

        $container = ContainerFactory::createFromPhp($file);

        $this->assertInstanceOf('\Aztech\Phinject\Container', $container);
    }

    public function testOverridenServiceBuilderFactoryIsUsedByFactory()
    {
        $serviceBuilder = $this->prophesize('Aztech\Phinject\ServiceBuilder');

        $serviceBuilderFactory = $this->prophesize('\Aztech\Phinject\ServiceBuilderFactory');
        $serviceBuilderFactory
            ->build(Argument::any())
            ->willReturn($serviceBuilder->reveal())
            ->shouldBeCalled();

        ContainerFactory::setServiceBuilderFactory($serviceBuilderFactory->reveal());
        ContainerFactory::createFromInlineYaml('');
        ContainerFactory::setServiceBuilderFactory(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWithInvalidConfigThrowsException()
    {
        $config = new \stdClass();

        ContainerFactory::create($config);
    }

    public function testTemplatedConfigsAreHandled()
    {
        $configSrc = <<<YML
templates:
    template:
        class: \stdClass
        properties:
            test: "{{word}}"

apply-templates:
    TemplatedObject:
        template: template
        apply:
            word: 'Hello world'
YML;

        $container = ContainerFactory::createFromInlineYaml($configSrc, [ 'templates' => true ]);
        $object = $container->get('TemplatedObject');

        $this->assertEquals('Hello world', $object->test);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingFileThrowsException()
    {
        $file = __DIR__ . '/noconfigfileshouldbehere.yml';

        ContainerFactory::create($file);
    }

    public function testUsingDeferredGeneratesLazyProxies()
    {
        $config = <<<YML
classes:
    deferred:
        class: \Aztech\Phinject\Tests\RefCountingClass
        lazy: true
        properties:
            test: 'hello'
YML;

        $this->assertEquals(0, RefCountingClass::getInstanceCount(), 'Count should be 0 initially.');

        $container = ContainerFactory::createFromInlineYaml($config, [ 'deferred' => true ]);
        $object = $container->get('deferred');

        $this->assertEquals(0, RefCountingClass::getInstanceCount(), 'Count should be 0 after lazy activation.');

        $this->assertEquals('hello', $object->test);
        $this->assertEquals(1, RefCountingClass::getInstanceCount());
    }
}

class RefCountingClass
{
    public static $counter = 0;

    public static function getInstanceCount()
    {
        return self::$counter;
    }

    public $test;

    public function __construct()
    {
        self::$counter++;
    }

    public function getTest()
    {
        return $this->test;
    }
}
