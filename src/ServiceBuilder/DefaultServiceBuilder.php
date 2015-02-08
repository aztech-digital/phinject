<?php

namespace Aztech\Phinject\ServiceBuilder;

use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\Container;
use Aztech\Phinject\ServiceBuilder;
use Aztech\Phinject\Initializer\OnceTypeInitializer;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\Util\ArrayResolver;

class DefaultServiceBuilder implements ServiceBuilder
{

    /**
     *
     * @var ActivatorFactory
     */
    protected $activatorFactory = null;

    /**
     *
     * @var InjectorFactory
     */
    protected $injectorFactory = null;

    /**
     *
     * @var TypeInitializer
     */
    protected $typeInitializer = null;

    /**
     *
     * @param ActivatorFactory $activatorFactory
     * @param InjectorFactory $injectorFactory
     */
    public function __construct(
        ActivatorFactory $activatorFactory,
        InjectorFactory $injectorFactory = null)
    {
        $this->activatorFactory = $activatorFactory;
        $this->injectorFactory = $injectorFactory;
        $this->typeInitializer = new OnceTypeInitializer();
    }

    /**
     * Chain of command of the class loader
     *
     * @param  ArrayResolver $serviceConfig
     * @param string $serviceName
     * @return object
     */
    public function buildService(Container $container, ArrayResolver $serviceConfig, $serviceName) {
        $object = $this->activate($container, $serviceConfig, $serviceName);

        // Singleton by default to prevent stack explosions
        if ($serviceConfig->resolve('singleton', true)) {
            // Only store if singleton'ed to spare memory
            $container->lateBind($serviceName, $object);
        }

        $this->inject($container, $object, $serviceConfig);

        return $object;
    }

    /**
     * Handles class instanciation
     *
     * @param ArrayResolver $serviceConfig
     * @param string $serviceName
     * @param Container $container
     * @return object
     */
    private function activate($container, ArrayResolver $serviceConfig, $serviceName)
    {
        $this->typeInitializer->initialize($container, $serviceConfig);

        $activator = $this->activatorFactory->getActivator($serviceName, $serviceConfig);

        return $activator->createInstance($container, $serviceConfig, $serviceName);
    }

    /**
     * Handle method invocations in the class
     *
     * @param ArrayResolver $serviceConfig
     * @param Container $container
     * @return boolean
     */
    private function inject($container, $object, ArrayResolver $serviceConfig)
    {
        if ($this->injectorFactory == null) {
            return true;
        }

        $injectors = $this->injectorFactory->getInjectors();

        foreach ($injectors as $injector) {
            $injector->inject($container, $serviceConfig, $object);
        }

        return true;
    }
}
