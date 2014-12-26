<?php

namespace Aztech\Phinject\Tests;

use Aztech\Phinject\UnknownDefinitionException;
class UnknownDefinitionExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceNameIsCorrectlySet()
    {
        $serviceName = 'myService';

        $exception = new UnknownDefinitionException($serviceName);

        $this->assertEquals($serviceName, $exception->getServiceName());
    }
}
