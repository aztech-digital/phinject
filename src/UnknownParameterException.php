<?php

namespace Aztech\Phinject;

class UnknownParameterException extends UnknownDefinitionException
{
    /**
     * @param string $serviceName
     */
    public function __construct($serviceName)
    {
        parent::__construct($serviceName);
    }
}
