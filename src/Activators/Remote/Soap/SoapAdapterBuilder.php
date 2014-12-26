<?php

namespace Aztech\Phinject\Activators\Remote\Soap;

use Aztech\Phinject\Activators\Remote\AdapterBuilder;
use ProxyManager\Factory\RemoteObject\Adapter\Soap;
use Zend\Soap\Client;

class SoapAdapterBuilder implements AdapterBuilder
{

    public function accepts($protocol)
    {
        return $protocol == 'soap';
    }

    public function build($endpoint)
    {
        return new Soap(new Client($endpoint));
    }
}
