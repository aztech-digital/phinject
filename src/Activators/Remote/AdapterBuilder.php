<?php

namespace Aztech\Phinject\Activators\Remote;

interface AdapterBuilder
{

    /**
     * @return boolean
     */
    function accepts($protocol);

    function build($endpoint);
}
