<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\DefaultReferenceResolver;
use Aztech\Phinject\UnknownDefinitionException;
use Aztech\Phinject\ObjectContainer;
use Aztech\Phinject\Config\ArrayConfig;
use Aztech\Phinject\ServiceBuilder\DefaultServiceBuilder;
use Aztech\Phinject\Activators\DefaultActivatorFactory;

class ReferenceResolverTest extends \PHPUnit_Framework_TestCase
{

    private $container;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('\Aztech\Phinject\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnValue('injected-service'));

        $this->container->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('injected-parameter'));
    }

    public function testContainerReferencesAreProperlyResolved()
    {
        $reference = '$container';

        $resolver = new DefaultReferenceResolver($this->container);
        $resolverResult = $resolver->resolve($reference);

        $this->assertSame($this->container, $resolverResult);
    }

    public function testConstantReferenceIsProperlyResolved()
    {
        define(md5(__METHOD__), 'foobarbaz');
        $reference = '$const.' . md5(__METHOD__);

        $resolver = new DefaultReferenceResolver($this->container);
        $resolverResult = $resolver->resolve($reference);

        $this->assertSame('foobarbaz', $resolverResult);
    }


    public function testEnvReferenceIsProperlyResolved()
    {
        putenv(md5(__METHOD__) . '=foobarbaz');
        $reference = '$env.' . md5(__METHOD__);

        $resolver = new DefaultReferenceResolver($this->container);
        $resolverResult = $resolver->resolve($reference);
        $this->assertSame('foobarbaz', $resolverResult);
    }

    public function testObjectReferencesAreProperlyResolved()
    {
        $reference = '@service';

        $resolver = new DefaultReferenceResolver($this->container);

        $this->assertEquals('injected-service', $resolver->resolve($reference));
    }

    public function testParameterReferencesAreProperlyResolved()
    {
        $reference = '%parameter';

        $resolver = new DefaultReferenceResolver($this->container);

        $this->assertEquals('injected-parameter', $resolver->resolve($reference));
    }

    public function testValueIsResolvedAsItself()
    {
        $reference = 'some value';

        $resolver = new DefaultReferenceResolver($this->container);

        $this->assertEquals($reference, $resolver->resolve($reference));
    }

    public function testResolveManyResolvesCorrectValues()
    {
        $references = array('@service', '%parameter', 'some value');
        $expected = array('injected-service', 'injected-parameter', 'some value');

        $resolver = new DefaultReferenceResolver($this->container);

        $this->assertEquals($expected, $resolver->resolveMany($references));
    }

    public function testNullCoalescenceResolvesDefaultValueWhenReferenceIsNotDefined()
    {
        $container = new ObjectContainer(
            new ArrayConfig([]),
            new DefaultServiceBuilder(new DefaultActivatorFactory())
        );

        $reference = '%parameter ?: default value';
        $resolver = new DefaultReferenceResolver($container);

        $this->assertEquals('default value', $resolver->resolve($reference));
    }
}
