<?php

namespace Aztech\Phinject\Injectors;

use Aztech\Phinject\Injector;

class InjectorFactory
{

    private $injectors = array();

    public function __construct()
    {
        $this->addInjector(new ClassHierarchyMethodInjector());
        $this->addInjector(new PropertyInjector());
        $this->addInjector(new MethodInjector());
        $this->addInjector(new OffsetSetInjector());
    }

    public function addInjector(Injector $injector)
    {
        $this->injectors[] = $injector;
    }

    public function getInjectors()
    {
        return $this->injectors;
    }
}
