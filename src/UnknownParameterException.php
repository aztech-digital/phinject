<?php

namespace Aztech\Phinject;

use Interop\Container\Exception\NotFoundException;

class UnknownParameterException extends UnknownDefinitionException implements NotFoundException
{
    /**
     * @param string $serviceName
     */
    public function __construct($serviceName)
    {
        parent::__construct($serviceName);
    }
}
