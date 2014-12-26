<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\Iterator;
class IteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testCount()
    {
        $iterator = new Iterator([ 'test', 'test']);

        $this->assertCount(2, $iterator);
    }

    public function testOffsetExists()
    {
        $iterator = new Iterator([ 'test' => true ]);

        $this->assertTrue(isset($iterator['test']));
        $this->assertTrue($iterator['test']);

        $this->assertFalse(isset($iterator['unset']));
    }

    public function testSetByOffset()
    {
        $iterator = new Iterator();

        $this->assertFalse(isset($iterator['test']));
        $this->assertFalse(isset($iterator['unset']));

        $iterator['test'] = true;

        $this->assertTrue(isset($iterator['test']));
        $this->assertTrue($iterator['test']);
        $this->assertFalse(isset($iterator['unset']));
    }

    public function testSetWithoutOffsetAppends()
    {
        $iterator = new Iterator();

        $this->assertFalse(isset($iterator[0]));
        $this->assertFalse(isset($iterator[1]));

        $iterator[] = true;

        $this->assertTrue(isset($iterator[0]));
        $this->assertTrue($iterator[0]);
        $this->assertFalse(isset($iterator[1]));
    }

    public function testUnset()
    {
        $iterator = new Iterator([ 'test' => true ]);

        $this->assertTrue(isset($iterator['test']));
        $this->assertTrue($iterator['test']);

        unset($iterator['test']);

        $this->assertFalse(isset($iterator['test']));
        $this->assertFalse(isset($iterator['unset']));
    }
}
