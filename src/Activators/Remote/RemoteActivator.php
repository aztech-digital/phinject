<?php
namespace Aztech\Phinject\Activators\Remote;

use Aztech\Phinject\Activator;
use Aztech\Phinject\Container;
use Aztech\Phinject\UnbuildableServiceException;
use Aztech\Phinject\Util\ArrayResolver;
use ProxyManager\Factory\RemoteObjectFactory;

class RemoteActivator implements Activator
{

    private $adapterFactory;

    public function __construct(RemoteAdapterFactory $adapterFactory)
    {
        $this->adapterFactory = $adapterFactory;
    }

    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName)
    {
        if (! isset($serviceConfig['remote']) || ! isset($serviceConfig['class'])) {
            throw new UnbuildableServiceException(
                sprintf("No remote configuration available for service '%'.", $serviceName));
        }

        $className = $serviceConfig['class'];
        $remoteConfig = $serviceConfig->resolve('remote');

        $adapter = $this->adapterFactory->getAdapter($serviceName, $remoteConfig);
        $factory = new RemoteObjectFactory($adapter);

        return $factory->createProxy($className);
    }
}
