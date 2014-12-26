<?php

namespace Aztech\Phinject\Activators\Remote\Rest;

use Aztech\Phinject\Activators\Remote\AdapterBuilder;
use Guzzle\Http\Client;

class RestAdapterBuilder implements AdapterBuilder
{

    public function accepts($protocol)
    {
        return $protocol == 'rest';
    }

    public function build($endpoint)
    {
        return new RestAdapter(new Client($endpoint));
    }
}
