<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\Util\ArrayResolver;

class ArrayResolverTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleResolutionReturnsCorrectValue()
    {
        $data = array('key' => 'value');

        $resolver = new ArrayResolver($data);

        $this->assertEquals('value', $resolver->resolve('key', null));
    }

    public function testSimpleResolutionOfMissingKeyReturnsDefaultValue()
    {
        $data = array();

        $resolver = new ArrayResolver($data);

        $this->assertEquals('default', $resolver->resolve('missing', 'default'));
    }

    public function testArrayResolutionReturnsArrayResolver()
    {
        $data = array('key' => array('sub-key' => 'value'));

        $resolver = new ArrayResolver($data);

        $this->assertInstanceOf('\Aztech\Phinject\Util\ArrayResolver', $resolver->resolve('key'));
    }

    public function testDottedResolutionReturnsCorrectValue()
    {
        $data = array('key' => array('sub-key' => 'value'));

        $resolver = new ArrayResolver($data);

        $this->assertEquals('value', $resolver->resolve('key.sub-key'));
    }

    public function testDottedResolutionOfMissingKeyReturnsCorrectValue()
    {
        $data = array('key' => array());

        $resolver = new ArrayResolver($data);

        $this->assertEquals('default', $resolver->resolve('key.sub-key', 'default'));
        $this->assertEquals('other-default', $resolver->resolve('key.other-key', 'other-default'));
    }

    public function testDottedResolutionOfSubKeyOnScalarReturnsDefaultValue()
    {
        $data = array('key' => true);

        $resolver = new ArrayResolver($data);

        $this->assertEquals('default', $resolver->resolve('key.sub-key', 'default'));
        $this->assertEquals('other-default', $resolver->resolve('key.other-key', 'other-default'));
    }

    public function testIterationReturnsCorrectlyWrappedValues()
    {
        $data = array(
          array(),
            array(),
            array()
        );

        $resolver = new ArrayResolver($data);

        foreach ($resolver as $item) {
            $this->assertTrue($item instanceof ArrayResolver);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResolveStrictThrowsException()
    {
        $data = new ArrayResolver([ 'present' => true ]);

        $data->resolveStrict('absent');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResolveStrictWithDottedExpressionThrowsException()
    {
        $data = new ArrayResolver([ 'present' => true ]);

        $data->resolveStrict('present.absent');
    }
}
