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
        $activators = $options->resolve(
            'activators',
            $options->resolve('plugins', [], false),
            false
        );

        foreach ($activators as $name => $activatorConfig)
        {
            if (is_string($activatorConfig)) {
                $activatorConfig = new ArrayResolver([ 'class' => $activatorConfig ]);
            }

            $activatorClass = $activatorConfig->resolveStrict('class');
            $key = $activatorConfig->resolve('key', $name);
            $activator =  new $activatorClass();

            $factory->addActivator($key, $activator);
        }
    }
}
