<?php

namespace Aztech\Phinject\Activators\Remote\Json;

use Aztech\Phinject\Activators\Remote\AdapterBuilder;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Zend\Json\Server\Client;

class JsonRpcAdapterBuilder implements AdapterBuilder
{

    public function accepts($protocol)
    {
        return $protocol == 'json-rpc';
    }

    public function build($endpoint)
    {
        return new JsonRpc(new Client($endpoint));
    }
}
