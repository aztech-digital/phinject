<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;
class DelegateContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testLookupIsNotDelegatedToContainer()
    {
        $yml = <<<YML
classes:
    dependency:
        class: \stdClass
        properties:
            test: 'hello'
    lookup:
        class: \stdClass
        properties:
            test: @dependency
YML;

        $delegateContainer = $this->prophesize('\Interop\Container\ContainerInterface');
        $delegateContainer->get('dependency')->willReturn(new \stdClass())->shouldBeCalled();

        $container = ContainerFactory::createFromInlineYaml($yml);
        $container->setDelegateContainer($delegateContainer->reveal());

        $container->get('lookup');
    }
}