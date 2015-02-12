<?php

namespace Aztech\Phinject\Tests\ResolverSpecifications;

use Aztech\Phinject\ContainerFactory;
class ServiceNameResolverSpecificationTest extends \PHPUnit_Framework_TestCase
{

    public function testServiceNameIsInjectedIntoService()
    {
        $yaml = <<<YML
classes:
    myNamedService:
        class: \stdClass
        properties:
            myName: \$name
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $service = $container->get('myNamedService');

        $this->assertEquals('myNamedService', $service->myName);
    }

    public function testDependencyServiceNameIsCorrectlyInjectedIntoDependency()
    {
        $yaml = <<<YML
classes:
    myNamedService:
        class: \stdClass
        properties:
            myName: \$name
            myDependency: '@myNamedDependency'
    myNamedDependency:
        class: \stdClass
        properties:
            myName: \$name
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);
        $service = $container->get('myNamedService');

        $this->assertEquals('myNamedService', $service->myName);
        $this->assertEquals('myNamedDependency', $service->myDependency->myName);
    }
}