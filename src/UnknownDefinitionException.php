<?php

namespace Aztech\Phinject;

use Interop\Container\Exception\NotFoundException;

class UnknownDefinitionException extends \RuntimeException implements NotFoundException
{
    private $serviceName;

    /**
     * @param string $serviceName
     */
    public function __construct($serviceName)
    {
        parent::__construct('Class not configured : ' . $serviceName);

        $this->serviceName = $serviceName;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }
}
