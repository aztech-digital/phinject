<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Activators\DefaultActivatorFactory;
use Aztech\Phinject\Injectors\InjectorFactory;
use Aztech\Phinject\ServiceBuilder\DefaultServiceBuilder;
use Aztech\Phinject\ServiceBuilder\LazyServiceBuilder;
use Aztech\Phinject\Util\ArrayResolver;

class ServiceBuilderFactory
{
    public function build(ArrayResolver $options)
    {
        $activatorFactory = new DefaultActivatorFactory();
        $injectorFactory = new InjectorFactory();

        $this->bindActivators($activatorFactory, $options);
        $this->bindInjectors($injectorFactory, $options);

        if ((bool) $options->resolve('deferred', true) == true) {
            $serviceBuilder = new LazyServiceBuilder($activatorFactory, $injectorFactory);
        }
        else {
            $serviceBuilder = new DefaultServiceBuilder($activatorFactory, $injectorFactory);
        }

        return $serviceBuilder;
    }

    private function bindActivators(ActivatorFactory $factory, ArrayResolver $options)
    {
        $activators = $options->resolveArray(
            'activators',
            $options->resolveArray('plugins', [])->extract()
        );

        foreach ($activators as $name => $activatorConfig)
        {
            if (is_string($activatorConfig)) {
                $activatorConfig = new ArrayResolver([ 'class' => $activatorConfig ]);
            }

            $key = $activatorConfig->resolve('key', $name);
            $activator = $this->buildActivator($key, $activatorConfig);

            $factory->addActivator($key, $activator);
        }
    }

    private function buildActivator($key, ArrayResolver $activatorConfig)
    {
        $activatorClass = $activatorConfig->resolveStrict('class');
        $activator =  new $activatorClass();

        if ($activator instanceof ConfigurationAware) {
            $activatorConfig = $activatorConfig->merge(
                new ArrayResolver([ 'key' => $key ])
            );

            $activator->setConfiguration($activatorConfig);
        }

        return $activator;
    }

    private function bindInjectors(InjectorFactory $factory, ArrayResolver $options)
    {
        $injectors = $options->resolveArray('injectors', []);

        foreach ($injectors as $name => $injectorConfig) {
            if (is_string($injectorConfig)) {
                $injectorConfig = new ArrayResolver([ 'class' => $injectorConfig ]);
            }

            $key = $injectorConfig->resolve('key', $name);
            $injector = $this->buildInjector($key, $injectorConfig);

            $factory->addInjector($injector);
        }
    }

    private function buildInjector($key, ArrayResolver $injectorConfig)
    {
        $injectorClass = $injectorConfig->resolveStrict('class');
        $injector = new $injectorClass();

        if ($injector instanceof ConfigurationAware) {
            $injectorConfig = $injectorConfig->merge(
                new ArrayResolver([ 'key' => $key ])
            );

            $injector->setConfiguration($injectorConfig);
        }

        return $injector;
    }
}
