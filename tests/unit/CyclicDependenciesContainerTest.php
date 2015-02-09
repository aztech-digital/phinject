<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\ContainerFactory;

class CyclicDependenciesContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testCircularDependenciesViaLazyObjects()
    {
        $config = <<<EOC
classes:
    depA:
        class: Aztech\Phinject\Tests\CircularDependency
        arguments: [ '@depB', 1 ]
        lazy: true
        singleton: true
    depB:
        class: Aztech\Phinject\Tests\CircularDependency
        arguments: [ '@depA', 2 ]
        lazy: true  
        singleton: true
EOC;
        
        $container = ContainerFactory::createFromInlineYaml($config, [ 'deferred' => true ]);
        
        $b = $container->get('depA')->getCircular();
        $a = $container->get('depB')->getCircular();
        
        $this->assertEquals(1, $a->getValue());
        $this->assertEquals(2, $b->getValue());
        
        $this->assertEquals($a->getCircular(), $b);
        $this->assertEquals($b->getCircular(), $a);
    }
}

class CircularDependency
{
    private $circular;
    
    private $value;
    
    public function __construct(self $other, $value)
    {
        $this->circular = $other;
        $this->value = $value;
    }
    
    public function getCircular()
    {
        return $this->circular;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}