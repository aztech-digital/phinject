<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Util\ArrayResolver;

class EmptyNodeValidator implements ConfigurationValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $array = $serviceNode->extract();

        if (empty($array)) {
            $validator->addError('Service configuration is empty.');
        }
    }
}
