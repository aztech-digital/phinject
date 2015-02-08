<?php

namespace Aztech\Phinject\Activators;

use Aztech\Phinject\Activator;
use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Activators\Reflection\AliasActivator;
use Aztech\Phinject\Activators\Reflection\InstanceInvocationActivator;
use Aztech\Phinject\Activators\Reflection\ReflectionActivator;
use Aztech\Phinject\Activators\Reflection\StaticInvocationActivator;
use Aztech\Phinject\Activators\Remote\RemoteActivator;
use Aztech\Phinject\Activators\Remote\RemoteAdapterFactory;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodNameParser;

class DefaultActivatorFactory implements ActivatorFactory
{

    private $activators = array();

    private $customActivators = array();

    private $methodNameParser;

    public function __construct()
    {
        $this->addInternalActivator('alias', new AliasActivator());
        $this->addInternalActivator('default', new ReflectionActivator());
        $this->addInternalActivator('builder', new InstanceInvocationActivator());
        $this->addInternalActivator('builder-static', new StaticInvocationActivator());
        $remoteFactory = new RemoteAdapterFactory();
        $this->addInternalActivator('remote', new RemoteActivator($remoteFactory));

        $this->methodNameParser = new MethodNameParser();
    }

    /**
     *
     * @param string $key
     */
    public function addActivator($key, Activator $activator)
    {
        if (array_key_exists($key, $this->activators)) {
            throw new \InvalidArgumentException('Activator key is reserved for internal use.');
        }

        $this->customActivators[$key] = $activator;
    }

    /**
     *
     * @param string $key
     */
    private function addInternalActivator($key, Activator $activator)
    {
        $this->activators[$key] = $activator;
    }

    /**
     *
     * @param string $serviceName
     * @param ArrayResolver $configuration
     * @throws UnbuildableServiceException
     * @return Activator
     */
    public function getActivator($serviceName, ArrayResolver $configuration)
    {
        if ($alias = $configuration->resolve('alias', null)) {
            return $this->activators['alias'];
        }

        if ($builder = $configuration->resolve('builder', null)) {
            return $this->getActivatorByBuilderType($serviceName, $builder);
        }

        if ($configuration->resolve('class', false) !== false) {
            return $this->getRemoteOrReflectionActivator($configuration);
        }

        foreach ($this->customActivators as $name => $activator) {
            if ($configuration->resolve($name, null)) {
                return $this->customActivators[$name];
            }
        }

        throw new UnbuildableServiceException(sprintf("Unbuildable service : '%s', no suitable activator found.", $serviceName));
    }

    /**
     * @param string $serviceName
     */
    private function getActivatorByBuilderType($serviceName, $builder)
    {
        $builderType = $this->getBuilderType($serviceName, $builder);

        if (array_key_exists($builderType, $this->activators)) {
            return $this->activators[$builderType];
        }

        throw new UnbuildableServiceException(sprintf("Unbuildable service : '%s', no suitable activator found.", $serviceName));
    }

    private function getBuilderType($serviceName, $builderKey)
    {
        if ($this->methodNameParser->isStaticInvocation($builderKey)) {
            return 'builder-static';
        }

        if ($this->methodNameParser->isInstanceInvocation($builderKey)) {
            return 'builder';
        }

        throw new UnbuildableServiceException(sprintf("Unbuildable service : '%s', no suitable activator found.", $serviceName));
    }

    /**
     * @param ArrayResolver $configuration
     */
    private function getRemoteOrReflectionActivator($configuration)
    {
        if ($configuration->resolve('remote', false)) {
            return $this->activators['remote'];
        }

        return $this->activators['default'];
    }
}
