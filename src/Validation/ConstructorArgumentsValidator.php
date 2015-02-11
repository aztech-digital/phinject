<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Util\ArrayResolver;

class ConstructorArgumentsValidator implements ConfigurationValidator
{

    /**
     *
     * @param string $serviceName
     */
    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $class = $serviceNode->resolve('class');

        if (! $this->validateClass($validator, $class)) {
            return;
        }

        if (! ($constructor = $this->validateAndExtractConstructor($class, $serviceNode, $validator))) {
            return;
        }

        $this->validateArgs($global, $constructor[0], $constructor[1], $validator);
    }

    /**
     *
     * @param Validator $validator
     */
    private function validateClass($validator, $class)
    {
        if ($class === null) {
            return false;
        }

        if (! class_exists($class) && ! interface_exists($class)) {
            $validator->addError('Class not found : ' . $class);
            return false;
        }

        return true;
    }

    private function resolveType(ArrayResolver $global, $reference)
    {
        $prefix = substr($reference, 0, 1);

        if ($prefix == '@') {
            return $global->resolve('classes.' . substr($reference, 1) . '.class');
        } else {
            return 'mixed';
        }
    }

    /**
     *
     * @param ArrayResolver $serviceNode
     * @param Validator $validator
     */
    private function validateAndExtractConstructor($class, $serviceNode, $validator)
    {
        $reflectionClass = new \ReflectionClass($class);

        /* @var $reflectionCtor \ReflectionMethod */
        $reflectionCtor = $reflectionClass->getConstructor();
        $ctorArgs = $serviceNode->resolve('arguments', array());

        if (! $this->validateConstructorExistence($validator, $reflectionCtor, $ctorArgs)) {
            return false;
        }

        $this->validateConstructorArgsCount($validator, $reflectionCtor, $ctorArgs);

        return array(
            $reflectionCtor,
            $ctorArgs
        );
    }

    /**
     *
     * @param \ReflectionMethod $reflectionCtor
     * @param Validator $validator
     */
    private function validateConstructorExistence($validator, $reflectionCtor, $ctorArgs)
    {
        if ($reflectionCtor != null) {
            return true;
        }

        if (! empty($ctorArgs->extract())) {
            $validator->addWarning('Constructor arguments are provided, but no constructor was found.');
        }

        return false;
    }

    /**
     *
     * @param \ReflectionMethod $reflectionCtor
     * @param Validator $validator
     */
    private function validateConstructorArgsCount($validator, $reflectionCtor, $ctorArgs)
    {
        if (count($ctorArgs) < $reflectionCtor->getNumberOfRequiredParameters()) {
            $validator->addError(sprintf('Invalid parameter count : %d instead of %d required.', count($ctorArgs), $reflectionCtor->getNumberOfRequiredParameters()));
        } elseif (count($ctorArgs) > $reflectionCtor->getNumberOfParameters()) {
            $validator->addWarning(sprintf('Invalid parameter count : %d instead of %d defined.', count($ctorArgs), $reflectionCtor->getNumberOfParameters()));
        }
    }

    /**
     *
     * @param ArrayResolver $global
     * @param \ReflectionMethod $reflectionCtor
     * @param Validator $validator
     */
    private function validateArgs($global, $reflectionCtor, $ctorArgs, $validator)
    {
        /* @var $parameter \ReflectionParameter */
        $i = 0;
        $args = $ctorArgs->extract();

        foreach ($reflectionCtor->getParameters() as $parameter) {
            if ($i >= count($args)) {
                break;
            }

            $this->validateArg($global, $reflectionCtor, validator, $i, $args, $parameter);
        }
    }

    private function validateArg($global, $reflectionCtor, $validator, & $i, $args, $parameter)
    {
        if (is_array($args[$i])) {
            return $this->validateService($validator, $global, 'Anonymous definition ' . $i, new ArrayResolver($args[$i]));
        }

        if (substr($args[$i], 0, 4) == '@ns:') {
            return $validator->addWarning('Namespace resolution syntax not supported yet. (triggered by ' . $args[$i] . ').');
        }

        $hint = $this->getHint($reflectionCtor->getDocComment(), $parameter->getName());
        $type = $this->resolveType($global, $args[$i]);

        $this->validateHint($validator, $type, $hint);

        $i ++;
    }

    /**
     *
     * @param string|null $hint
     * @param Validator $validator
     */
    private function validateHint($validator, $type, $hint)
    {
        if ($hint == null || $type == 'mixed') {
            return;
        }

        if (! $this->validateHintEqualsType($hint, $type)) {
            $validator->addWarning(sprintf('Parameter type mismatch : %s instead of %s defined.', $type, $hint));
        }
    }

    /**
     *
     * @param string $hint
     */
    private function validateHintEqualsType($hint, $type)
    {
        $typeParts = explode('\\', $type);

        return ! ($type != $hint && $hint != array_pop($typeParts));
    }

    /**
     *
     * @param string $docComment
     * @param string $varName
     */
    private function getHint($docComment, $varName)
    {
        $matches = array();
        $count = preg_match_all('/@param[\t\s]*(?P<type>[^\t\s]*)[\t\s]*\$(?P<name>[^\t\s]*)/sim', $docComment, $matches);
        if ($count > 0) {
            foreach ($matches['name'] as $n => $name) {
                if ($name == $varName) {
                    return $matches['type'][$n];
                }
            }
        }
        return null;
    }
}
