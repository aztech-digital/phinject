<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Util\ArrayResolver;

class AliasedConfigProcessor
{

    /**
     * Processes a configuration node to expand the templates it contains.
     *
     * @param ArrayResolver $config The node containing the configuration templates and templated instances.
     * @return ArrayResolver An array resolver containing the expanded templates.
     */
    public static function process(ArrayResolver $config)
    {
        $copy = $config->extract();
        $instances = $config->resolve('classes', array());

        foreach ($instances as $name => $instance) {
            self::processAliases($config, $copy, $name, $instance);
        }

        return new ArrayResolver($copy);
    }

    /**
     *
     * @param ArrayResolver $config
     * @param mixed[] $copy
     * @param string $name
     * @param ArrayResolver $instanceDefinition
     */
    private static function processAliases(ArrayResolver $config, array & $copy, $name, ArrayResolver $instanceDefinition)
    {
        $classText = json_encode($instanceDefinition->extract());
        $aliases = $config->resolve('aliases', array());

        foreach ($aliases as $alias => $definition) {
            $classText = str_replace(sprintf('$:%s', $alias), addslashes($definition), $classText);
        }

        $copy['classes'][$name] = json_decode($classText, true);
    }
}
