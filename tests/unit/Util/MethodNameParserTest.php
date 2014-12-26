<?php

namespace Aztech\Phinject\Tests\Util;

use Aztech\Phinject\Util\MethodNameParser;

class MethodNameParserTest extends \PHPUnit_Framework_TestCase
{

    public function getStaticTypeTestSet()
    {
        /* @f:off */
        return array(
            array('\\SomeType::invoke', true),
            array('\\SomeType::invoke()', true),
            array('@SomeInstance->invoke()', false),
            array('@SomeInstance->invoke', false)
        );
        /* @f:on */
    }

    /**
     * @dataProvider getStaticTypeTestSet
     */
    public function testStaticTypeIsCorrectlyDetected($invoke, $expected)
    {
        $this->assertEquals($expected, MethodNameParser::getMethodInvocation($invoke)->isStatic());
    }

    public function getMethodNameTestSet()
    {
        /* @f:off */
        return array(
            array('\\SomeType::invoke', 'invoke'),
            array('\\SomeType::invoke()', 'invoke'),
            array('@SomeInstance->invoke()', 'invoke'),
            array('@SomeInstance->invoke', 'invoke')
        );
        /* @f:on */
    }

    /**
     * @dataProvider getMethodNameTestSet
     */
    public function testMethodNameIsCorrectlyDetected($invoke, $expectedName)
    {
        $this->assertEquals($expectedName, MethodNameParser::getMethodInvocation($invoke)->getName());
    }


    public function getOwnerTestSet()
    {
        /* @f:off */
        return array(
            array('\\SomeType::invoke', '\\SomeType'),
            array('\\SomeType::invoke()', '\\SomeType'),
            array('@SomeInstance->invoke()', '@SomeInstance'),
            array('@SomeInstance->invoke', '@SomeInstance')
        );
        /* @f:on */
    }

    /**
     * @dataProvider getOwnerTestSet
     */
    public function testOwnerIsCorrectlyDetected($invoke, $expectedOwner)
    {
        $this->assertEquals($expectedOwner, MethodNameParser::getMethodInvocation($invoke)->getOwner());
    }

    public function getArgsTestSet()
    {
        /* @f:off */
        return array(
            array('\\SomeType::invoke', false, null),
            array('\\SomeType::invoke()', true, array()),
            array('@SomeInstance->invoke()', true, array()),
            array('@SomeInstance->invoke', false, null),
            array('@SomeInstance->invoke(bla, %test,    @AnotherInstance    )', true, array('bla', '%test', '@AnotherInstance')),
            array('@SomeInstance->invoke(@AnotherInstance)', true, array('@AnotherInstance')),
            array('@SomeInstance->invoke(,@AnotherInstance)', true, array('', '@AnotherInstance')),
            array('@SomeInstance->invoke(@AnotherInstance,)', true, array('@AnotherInstance', '')),
        );
        /* @f:on */
    }

    /**
     * @dataProvider getArgsTestSet
     */
    public function testArgsAreCorrectlyDetected($invoke, $shouldHaveArgs, $expectedArgs = null)
    {
        $method = MethodNameParser::getMethodInvocation($invoke);

        $this->assertEquals($shouldHaveArgs, $method->hasArguments());
        $this->assertEquals($expectedArgs, $method->getArguments());
    }
}
