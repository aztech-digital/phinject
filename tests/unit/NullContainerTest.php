<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\NullContainer;

class NullContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testHasAlwaysReturnsFalse()
    {
        $container = new NullContainer();

        $this->assertFalse($container->has('any'));
    }

    /**
     * @expectedException Interop\Container\Exception\NotFoundException
     */
    public function testGetThrowsException()
    {
        $container = new NullContainer();

        $container->get('any');
    }
}