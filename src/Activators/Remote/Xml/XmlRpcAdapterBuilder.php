<?php

namespace Aztech\Phinject\Activators\Remote\Xml;

use Aztech\Phinject\Activators\Remote\AdapterBuilder;
use ProxyManager\Factory\RemoteObject\Adapter\XmlRpc;
use Zend\XmlRpc\Client;

class XmlRpcAdapterBuilder implements AdapterBuilder
{

    public function accepts($protocol)
    {
        return $protocol == 'xml-rpc';
    }

    public function build($endpoint)
    {
        return new XmlRpc(new Client($endpoint));
    }
}
