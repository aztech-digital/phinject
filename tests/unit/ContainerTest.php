<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\Container;
use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\ContainerFactory;
use Aztech\Phinject\Config\ArrayConfig;
use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\Config\InlineConfig;
use Aztech\Phinject\Config\Parser\YamlParser;
use Aztech\Phinject\ObjectContainer;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Aztech\Phinject\UnknownDefinitionException
     */
    public function testContainerThrowsExceptionOnMissingServiceName()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('doLoad')
            ->will($this->returnValue(array()));

        $container = ContainerFactory::create($config);

        $container->get('UnknownService');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testContainerThrowsExceptionOnMissingDependency()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('doLoad')
            ->will($this->returnValue(array(
                'classes' => array(
                    'service' => array('class' => '\stdClass', 'props' => array('dep' => '@missing-service'))
            ))));

        $container = ContainerFactory::create($config);

        $container->get('service');
    }

    public function testContainerReturnsCorrectParameterValue()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('doLoad')
            ->will($this->returnValue(array(
                'parameters' => array(
                    'param' => 'value'
                ))));

        $container = ContainerFactory::create($config);

        $this->assertEquals('value', $container->getParameter('param'));
    }

    /**
     * @param string $name
     */
    private function getCyclicDependencies($name, $singletonForFirst = false, $singletonForOther = false)
    {
        $first = array(
            'class' => '\stdClass',
            'singleton' => $singletonForFirst,
            'properties' => array(
                'cyclic' => '@' . $name . '-dependency'
            )
        );
        $second = array(
            'class' => '\stdClass',
            'singleton' => $singletonForOther,
            'properties' => array(
                'cyclic' => '@' . $name
            )
        );

        return array('classes' => array($name => $first, $name . '-dependency' => $second));
    }

    public function testCyclicDependenciesDoNotOverflowWithOneSingletonInCycle()
    {
        $config = new ArrayConfig($this->getCyclicDependencies('cyclic', true, false));
        $container = ContainerFactory::create($config, array('deferred' => true));

        $container->get('cyclic');
    }

    public function testCyclicDependenciesDoNotOverflowWithTwoSingletonsInCycle()
    {
        $config = new ArrayConfig($this->getCyclicDependencies('cyclic', true, true));

        $container = ContainerFactory::create($config, array('deferred' => true));

        $container->get('cyclic');
    }

    public function testResolvingAManuallyBoundObjectReturnsCorrectInstance()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('classes' => array())));

        $container = ContainerFactory::create($config, array('deferred' => true));

        $item = new \stdClass();

        $container->bind('boundKey', $item);

        $this->assertSame($item, $container->get('boundKey'));

        $item = new \stdClass();

        $this->assertNotSame($item, $container->get('boundKey'));
    }

    public function testResolvingAManuallyBoundObjectDefinitionReturnsCorrectInstance()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('classes' => array())));

        $container = ContainerFactory::create($config, array('deferred' => true));

        $itemDefinition = array(
            'class' => '\stdClass',
            'props' => array(
                'dummy' => 'dummy-value'
            )
        );

        $container->bind('boundKey', $itemDefinition);

        $item = $container->get('boundKey');

        $this->assertEquals('dummy-value', $item->dummy);
    }

    public function testResolvingAManuallyLateBoundObjectReturnsCorrectInstance()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('classes' => array())));

        $container = ContainerFactory::create($config, array('deferred' => false));

        $item = new \stdClass();

        $container->lateBind('boundKey', $item);

        $item = new \stdClass();

        $this->assertSame($item, $container->get('boundKey'));

        $item = new \stdClass();

        $this->assertSame($item, $container->get('boundKey'));
    }

    public function testResolvingOutOfScopeLateBoundObjectsReturnsNonNullInstance()
    {
        $test = function ($container) {
            $bla = new \stdClass();
            $container->lateBind('test', $bla);
        };

        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('classes' => array())));

        $container = ContainerFactory::create($config, array('deferred' => false));

        $test($container);
        $test = null;

        $this->assertNotNull($container->get('test'));
    }

    /**
     * @expectedException \Aztech\Phinject\IllegalTypeException
     */
    public function testAddingAParameterCallableThrowsException()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('parameters' => array(), 'classes' => array())));

        $container = ContainerFactory::create($config);

        $container->setParameter('dummy.key', function() { });
    }

    /**
     * @expectedException \Aztech\Phinject\IllegalTypeException
     */
    public function testAddingAParameterWithCallableInArrayThrowsException()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('parameters' => array(), 'classes' => array())));

        $container = ContainerFactory::create($config);

        $container->setParameter('dummy.key', array('dummy-key1' =>'value1', 'dummy-key2' => function() {}));
    }


    /**
     * @expectedException \Aztech\Phinject\IllegalTypeException
     */
    public function testAddingAParameterWithCallableInArrayMultiDimensionalThrowsException()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('parameters' => array(), 'classes' => array())));

        $container = ContainerFactory::create($config);

        $container->setParameter('dummy.key', array('dummy-key1' =>'value1', "sub" => array('dummy-key2' => function() {})));
    }


    /**
     * @expectedException \Aztech\Phinject\IllegalTypeException
     */
    public function testAddingAParameterWithObjectInArrayMultiDimensionalThrowsException()
    {
        $config = $this->getMockBuilder('\Aztech\Phinject\Config\AbstractConfig')
            ->getMockForAbstractClass();

        $config->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array('parameters' => array(), 'classes' => array())));

        $container = ContainerFactory::create($config);

        $container->setParameter('dummy.key', array('dummy-key1' =>'value1', "sub" => array('dummy-key2' => new \stdClass())));
    }

    public function testGettingMultiDimensionalParameterReturnCorrectValue()
    {
        $yml = <<<YML
parameters :
    dummy :
        key : dummy-value
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);

        $value = $container->getParameter('dummy.key');

        $this->assertSame('dummy-value', $value);
    }

    public function testAddingAParameterInMultiDimensionialReturnSame()
    {
        $yml = <<<YML
parameters :
    dummy :
        key : dummy-value
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);

        $container->setParameter('dummy.key2', 'dummy-value2');
        $value = $container->getParameter('dummy.key2');
        $this->assertSame('dummy-value2', $value);
    }

    public function testAddingAnArrayParameterInMultiDimensionialReturnSame()
    {
        $yml = <<<YML
parameters : []
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);
        $dbConfig = array("host" => "127.0.0.1", "port" => 5432);
        $container->setParameter('dummy', array('db' => $dbConfig));
        $host = $container->getParameter('dummy.db.host');
        $port = $container->getParameter('dummy.db.port');
        $db = $container->getParameter('dummy.db');
        $this->assertSame('127.0.0.1', $host);
        $this->assertSame(5432, $port);
        $this->assertSame($dbConfig, $db);
    }

    public function testAddingParameterWontEraseCollateralData()
    {
        $yml = <<<YML
parameters :
    dummy :
        key : dummy-value
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);
        $container->setParameter('dummy', array('db' => array("host" => "127.0.0.1", "port" => 5432)));
        $this->assertSame('dummy-value', $container->getParameter('dummy.key'));
    }

    public function testResolvingNamespaceReturnsAllObjectsInNamespace()
    {
        $yml = <<<YML
classes:
    namespace:dummy:
        class: \stdClass
        properties:
            test: 'hello'
    namespace:other:
        class: \stdClass
        properties:
            test: 'world'
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);
        $objects = array_values($container->getNamespace('namespace'));

        $this->assertCount(2, $objects);
        $this->assertInstanceOf('\stdClass', $objects[0]);
        $this->assertEquals('hello', $objects[0]->test);
        $this->assertInstanceOf('\stdClass', $objects[1]);
        $this->assertEquals('world', $objects[1]->test);
    }

    public function testInjectingNamespaceInjectsArrayOfObjects()
    {
        $yml = <<<YML
classes:
    namespace:dummy:
        class: \stdClass
        properties:
            test: 'hello'
    namespace:other:
        class: \stdClass
        properties:
            test: 'world'
    regular:
        class: \stdClass
        properties:
            test: \$ns:namespace
YML;

        $container = ContainerFactory::createFromInlineYaml($yml);
        $object = $container->get('regular');

        $this->assertCount(2, $object->test);

        $objects = array_values($object->test);

        $this->assertInstanceOf('\stdClass', $objects[0]);
        $this->assertEquals('hello', $objects[0]->test);
        $this->assertInstanceOf('\stdClass', $objects[1]);
        $this->assertEquals('world', $objects[1]->test);
    }

    public function testFlushForcesRebuildOfServices()
    {
        $config = <<<YML
classes:
  Test:
    class: \stdClass
YML;

        $builder = $this->getMockBuilder('\Aztech\Phinject\ServiceBuilder')
            ->disableOriginalConstructor()
            ->setMethods([ 'buildService' ])
            ->getMock();

        $builder->expects($this->exactly(2))
            ->method('buildService')
            ->willReturn(new \stdClass());

        $config = new InlineConfig(new YamlParser(), $config);
        $container = new ObjectContainer($config, $builder);

        $container->get('Test');
        $container->flushRegistry();
        $container->get('Test');
    }

    public function testLateBinding()
    {
        $object = new \stdClass();
        $container = ContainerFactory::create(new ArrayConfig([]));

        $container->lateBind('Test', $object);

        $object->test = 'hello world';

        $this->assertEquals('hello world', $container->get('Test')->test);

        $object = new \stdClass();

        $this->assertObjectNotHasAttribute('test', $container->get('Test'));
    }

    public function testLateBindingWithArray()
    {
        $definition = [ 'class' => '\stdClass', 'properties' => [ 'test' => 'hello' ]];
        $container = ContainerFactory::create(new ArrayConfig([]));

        $container->lateBind('Test', $definition);

        $this->assertObjectHasAttribute('test', $container->get('Test'));
        $this->assertEquals('hello', $container->get('Test')->test);
    }

    public function testResolveMany()
    {
        $config = <<<YML
classes:
    a:
        class: \stdClass
    b:
        class: \stdClass
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $objects = $container->resolveMany([ '@a', '@b' ]);

        $this->assertCount(2, $objects);
    }

    public function testResolveArrayDefinition()
    {
        $definition = [ 'class' => '\stdClass', 'properties' => [ 'test' => 'hello' ]];
        $container = ContainerFactory::create(new ArrayConfig([]));

        $object = $container->resolve($definition);

        $this->assertInstanceOf('\stdClass', $object);
        $this->assertObjectHasAttribute('test', $object);
        $this->assertEquals('hello', $object->test);
    }

    /**
     * @expectedException \Aztech\Phinject\UnknownDefinitionException
     */
    public function testResolveMissingThrowsException()
    {
        $container = ContainerFactory::create(new ArrayConfig([]));

        $container->resolve('@missing');
    }

    public function testSingleArgumentsArraysCanBePassedAsScalars()
    {
        $config = <<<YML
classes:
    a:
        class: \DateTime
        call:
            setTimestamp: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $object = $container->resolve('@a');

        $this->assertEquals(42, $object->getTimestamp());
    }

    public function testResolveNullCoalescedDefinedValueReturnsPossiblyNullValue()
    {
        $config = <<<YML
parameters:
    timestamp: 23

classes:
    b:
        class: \DateTime
        call:
            setTimestamp: %timestamp ?: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $object = $container->resolve('@b');

        $this->assertEquals(23, $object->getTimestamp());
    }

    public function testResolveNullCoalescedNullValueReturnsValueIfNull()
    {
        $config = <<<YML
classes:
    b:
        class: \DateTime
        call:
            setTimestamp: %timestamp ?: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $object = $container->resolve('@b');

        $this->assertEquals(42, $object->getTimestamp());
    }

    public function testResolveNestedNullCoalescedNullValuesReturnsNestedValueIfNull()
    {
        $config = <<<YML
classes:
    b:
        class: \DateTime
        call:
            setTimestamp: %timestamp ?: %missingToo ?: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $object = $container->resolve('@b');

        $this->assertEquals(42, $object->getTimestamp());
    }

    public function testResolveDeferredValueReturnsCallback()
    {
        $config = <<<YML
classes:
    b:
        class: \DateTime
        call:
            setTimestamp: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $object = $container->resolve('$defer:@b->getTimestamp()');

        $this->assertEquals(42, $object());
    }

    public function testResolveDeferredValueExtraTests()
    {
        $config = <<<YML
parameters:
    ts: 23
classes:
    b:
        class: \DateTime
        call:
            setTimestamp: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $getTs = $container->resolve('$defer:@b->getTimestamp()');
        $updateTs = $container->resolve('$defer:@b->setTimestamp(12)');
        $updateTsParam = $container->resolve('$defer:@b->setTimestamp(%ts)');
        $updateTsParamless = $container->resolve('$defer:@b->setTimestamp');

        $this->assertEquals(42, $getTs());
        $updateTs();
        $this->assertEquals(12, $getTs());
        $updateTsParam();
        $this->assertEquals(23, $getTs());
        $updateTsParamless(7);
        $this->assertEquals(7, $getTs());
    }

    /**
     * @expectedException \Aztech\Phinject\InvalidReferenceException
     */
    public function testResolveThrowsExceptionWithNonMethodCalls()
    {
        $config = <<<YML
parameters:
    ts: 23
classes:
    b:
        class: \DateTime
        call:
            setTimestamp: 42
YML;

        $container = ContainerFactory::createFromInlineYaml($config);
        $container->resolve('$defer:@b');
    }
}
