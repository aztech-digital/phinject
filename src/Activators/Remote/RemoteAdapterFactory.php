<?php

namespace Aztech\Phinject\Activators\Remote;

use Aztech\Phinject\Activators\Remote\Json\JsonRpcAdapterBuilder;
use Aztech\Phinject\Activators\Remote\Rest\RestAdapterBuilder;
use Aztech\Phinject\Activators\Remote\Soap\SoapAdapterBuilder;
use Aztech\Phinject\Activators\Remote\Xml\XmlRpcAdapterBuilder;
use Aztech\Phinject\Util\ArrayResolver;
use ProxyManager\Factory\RemoteObject\AdapterInterface;

class RemoteAdapterFactory
{

    private $builders = array();

    public function __construct()
    {
        $this->builders[] = new JsonRpcAdapterBuilder();
        $this->builders[] = new RestAdapterBuilder();
        $this->builders[] = new SoapAdapterBuilder();
        $this->builders[] = new XmlRpcAdapterBuilder();
    }

    /**
     *
     * @param string $serviceName
     * @param ArrayResolver $serviceConfig
     * @throws UnknownProtocolException
     * @return AdapterInterface
     */
    public function getAdapter($serviceName, ArrayResolver $serviceConfig)
    {
        $this->validateConfig($serviceConfig, $serviceName);

        $protocol = $serviceConfig['protocol'];
        $endpoint = $serviceConfig['endpoint'];

        foreach ($this->builders as $builder) {
            if ($builder->accepts($protocol)) {
                return $builder->build($endpoint);
            }
        }

        throw new UnknownProtocolException(sprintf("Protocol '%s' is not supported ", $protocol));
    }

    /**
     *
     * @param string $serviceName
     * @param ArrayResolver $serviceConfig
     */
    private function validateConfig($serviceConfig, $serviceName)
    {
        if (! isset($serviceConfig['protocol']) || ! isset($serviceConfig['endpoint'])) {
            throw new \InvalidArgumentException(
                sprintf("Protocol and endpoint are required for remote object '%s'", $serviceName));
        }
    }
}
