<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Util\ArrayResolver;

class DependencyValidator implements ConfigurationValidator
{

    private $ignoredClasses = array();

    public function ignore($class)
    {
        $this->ignoredClasses[] = $class;
    }

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $this->validateNode($validator, $global, $serviceNode->resolve('arguments', array()));

        $className = $serviceNode->resolve('class');
        $properties = $serviceNode->resolve('props', array());
        $this->validateNode($validator, $global, $properties);

        if ($className == '\stdClass' || ! class_exists($className)) {
            return;
        }

        $names = array_keys($properties);

        foreach ($names as $propertyName) {
            $this->validateProperty($validator, $className, $propertyName);
        }
    }

    /**
     * @param Validator $validator
     */
    private function validateProperty($validator, $className, $propertyName)
    {
        if (property_exists($className, $propertyName)) {
            continue;
        }

        if (method_exists($className, '__set')) {
            $validator->addWarning(sprintf("Undefined target property'%s', but a magic set method was found.", $propertyName));
        } else {
            $validator->addError(sprintf("Undefined target property'%s'.", $propertyName));
        }
    }

    private function validateNode(Validator $validator, ArrayResolver $global, ArrayResolver $node)
    {
        foreach ($node as $arg) {
            $prefix = substr($arg, 0, 1);
            $partial = substr($arg, 1);

            if ($prefix == '@') {
                $this->validateServiceNode($validator, $global, $partial);
            } elseif ($prefix == '%') {
                $this->validateParamNode($validator, $global, $partial);
            }
        }
    }

    /**
     * @param Validator $validator
     * @param ArrayResolver $global
     * @param string $partial
     */
    private function validateServiceNode($validator, $global, $partial)
    {
        $dependencyNode = $global->resolve('classes.' . $partial, null);

        if ($dependencyNode === null && ! in_array($partial, $this->ignoredClasses)) {
            $validator->addError('Missing dependency : ' . $partial);
        }
    }

    /**
     * @param Validator $validator
     * @param ArrayResolver $global
     * @param string $partial
     */
    private function validateParamNode($validator, $global, $partial)
    {
        $parameterNode = $global->resolve('parameters.' . $partial, null);

        if ($parameterNode === null) {
            $validator->addError('Missing parameter definition : ' . $partial);
        }
    }
}
