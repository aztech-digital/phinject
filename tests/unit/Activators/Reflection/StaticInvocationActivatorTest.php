<?php
namespace Aztech\Phinject\Tests\Activators\Reflection;

use Aztech\Phinject\Activators\Reflection\StaticInvocationActivator;
use Aztech\Phinject\Util\ArrayResolver;

class StaticInvocationActivatorTest extends \PHPUnit_Framework_TestCase
{

    protected static $service;

    protected $container;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();

        self::$service = new \stdClass();
    }

    public static function stubFactoryMethod()
    {
        return self::$service;
    }

    /**
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testActicationFailsWithMissingClass()
    {
        $activator = new StaticInvocationActivator();

        $instance = $activator->createInstance($this->container,
            new ArrayResolver(array('builder' => '\Aztech\Phinject\UnpossiblyFindableClass::unknownFactoryMethod')), 'dependency');
    }

    /**
     * @expectedException \Aztech\Phinject\UnbuildableServiceException
     */
    public function testActicationFailsWithMissingMethod()
    {
        $activator = new StaticInvocationActivator();

        $instance = $activator->createInstance($this->container,
            new ArrayResolver(array('builder' => '\Aztech\Phinject\Tests\Activators\Reflection\StaticInvocationActivatorTest::unknownFactoryMethod')), 'dependency');
    }

    public function testActivationSucceedsWithNoArgs()
    {
        $activator = new StaticInvocationActivator();

        $instance = $activator->createInstance($this->container,
            new ArrayResolver(array('builder' => '\Aztech\Phinject\Tests\Activators\Reflection\StaticInvocationActivatorTest::stubFactoryMethod')), 'dependency');

        $this->assertSame(self::$service, $instance);
    }
}
