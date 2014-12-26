<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Util\ArrayResolver;

interface ConfigurationValidator
{
    /**
     * @return void
     */
    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode);
}
