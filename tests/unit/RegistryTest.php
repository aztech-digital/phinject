<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ObjectRegistry;

class ObjectRegistryTest extends \PHPUnit_Framework_TestCase
{

    public function testFlushClearsRegistry()
    {
        $registry = new ObjectRegistry();
        $value = 'value';

        $registry->set('key', $value);
        $registry->flush();

        $this->assertNull($registry->get('key'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetThrowsExceptionWithExceptionFlagOn()
    {
        $registry = new ObjectRegistry();

        $registry->getStrict('unknownKey');
    }

    public function testGetReturnsNullWithExceptionFlagOff()
    {
        $registry = new ObjectRegistry();

        $this->assertNull($registry->get('unknownKey'));
    }

    public function testGetReturnsSetValue()
    {
        $registry = new ObjectRegistry();
        $value = 'myValue';

        $registry->set('key', $value);

        $this->assertEquals($value, $registry->get('key'));
    }

    public function testObjectRegistryStoresCorrectObjectReferences()
    {
        $registry = new ObjectRegistry();
        $value = new \stdClass();

        $registry->set('key', $value);

        $value->property = 'modified';

        $this->assertEquals('modified', $registry->get('key')->property);
        $this->assertSame($value, $registry->get('key'));
    }

}
