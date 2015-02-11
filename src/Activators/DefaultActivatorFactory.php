<?php

namespace Aztech\Phinject\Activators;

use Aztech\Phinject\Activator;
use Aztech\Phinject\ActivatorFactory;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Activators\Reflection\AliasActivator;
use Aztech\Phinject\Activators\Reflection\InvocationActivator;
use Aztech\Phinject\Activators\Reflection\ReflectionActivator;
use Aztech\Phinject\Activators\Remote\RemoteActivator;
use Aztech\Phinject\Activators\Remote\RemoteAdapterFactory;
use Aztech\Phinject\Util\ArrayResolver;

class DefaultActivatorFactory implements ActivatorFactory
{

    private $activators = array();

    private $customActivators = array();

    public function __construct(RemoteAdapterFactory $remoteFactory = null)
    {
        $remoteFactory = $remoteFactory ?: new RemoteAdapterFactory();

        $this->addInternalActivator('remote', new RemoteActivator($remoteFactory));
        $this->addInternalActivator('builder', new InvocationActivator());
        $this->addInternalActivator('alias', new AliasActivator());
        $this->addInternalActivator('class', new ReflectionActivator());
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
        if ($activator = $this->resolveActivator($this->customActivators, $configuration)) {
            return $activator;
        }

        if ($activator = $this->resolveActivator($this->activators, $configuration)) {
            return $activator;
        }

        throw new UnbuildableServiceException(sprintf("Unbuildable service : '%s', no suitable activator found.", $serviceName));
    }

    private function resolveActivator(array $activators, ArrayResolver $configuration)
    {
        foreach ($activators as $name => $activator) {
            if ($configuration->resolve($name, null)) {
                return $activator;
            }
        }

        return null;
    }
}
