<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Util\ArrayResolver;

class CyclicDependencyValidator implements ConfigurationValidator
{

    private $currentNodeName;

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $this->currentNodeName = $serviceName;

        $properties = $serviceNode->resolveArray('props', $serviceNode->resolveArray('properties', [])
            ->extract());

        foreach ($properties as $name) {
            $name = substr($name, 1);
            $dependencyNode = $global->resolveArray('classes.' . $name, []);

            $this->validateProperty($validator, $serviceNode, $dependencyNode, $name);
        }
    }

    /**
     *
     * @param validator
     */
    private function validateProperty($validator, $serviceNode, $dependencyNode, $name)
    {
        if (! $this->dependsOnCurrentNode($dependencyNode)) {
            return;
        }

        if ($this->hasOneSingleton($serviceNode, $dependencyNode)) {
            return $validator->addWarning('Cyclic dependency detected with ' . $name);
        }

        return $validator->addError('Unsatisfiable cyclic dependency detected with ' . $name);
    }

    private function dependsOnCurrentNode(ArrayResolver $config)
    {
        $dependencies = $config->resolveArray('props', $config->resolveArray('properties', [])
            ->extract());

        foreach ($dependencies as $dependency) {
            if (substr($dependency, 0, 1) == '@' && substr($dependency, 1) == $this->currentNodeName) {
                return true;
            }
        }

        return false;
    }

    private function hasOneSingleton(ArrayResolver $serviceNode, ArrayResolver $dependencyNode)
    {
        if ($serviceNode->resolve('singleton', false) == true) {
            return true;
        }

        return $dependencyNode->resolve('singleton', false) == true;
    }
}
