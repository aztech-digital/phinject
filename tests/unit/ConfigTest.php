<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ReferenceResolver;
use Aztech\Phinject\ContainerFactory;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentVariablesAreCorrectlyExpandedInIncludes()
    {
        $container = ContainerFactory::createFromYaml(__DIR__ . '/../res/config.yml');

        $this->assertEquals(getenv('HOME'), $container->getParameter('test'));
        $this->assertEquals('$env.HOME', $container->getParameter('safe'));
    }
}
