<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Util\ArrayResolver;

class TemplatedConfigProcessor
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
        $instances = $config->resolveArray('apply-templates', []);

        foreach ($instances as $name => $instance) {
            self::processTemplate($config, $copy, $name, $instance);
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
    private static function processTemplate(ArrayResolver $config, array & $copy, $name, ArrayResolver $instanceDefinition)
    {
        $templateName = self::extractTemplateName($instanceDefinition, $name);
        $template = self::extractTemplate($config, $templateName);

        $templateText = json_encode($template->extract());
        $variables = $instanceDefinition->resolveArray('apply', []);

        foreach ($variables as $variable => $value) {
            $templateText = str_replace(sprintf('{{%s}}', $variable), $value, $templateText);
        }

        $copy['classes'][$name] = json_decode($templateText, true);
    }

    /**
     *
     * @param ArrayResolver $instanceDefinition
     * @param string $name
     * @throws \RuntimeException
     * @return string|null
     */
    private static function extractTemplateName(ArrayResolver $instanceDefinition, $name)
    {
        $templateName = $instanceDefinition->resolve('template', null);

        if (! $templateName) {
            throw new \RuntimeException("Template name not declared in '$name'.");
        }

        return (string) $templateName;
    }

    /**
     *
     * @param ArrayResolver $config
     * @param string $templateName
     * @throws \RuntimeException
     * @return mixed
     */
    private static function extractTemplate(ArrayResolver $config, $templateName)
    {
        $template = $config->resolve("templates.$templateName", null);

        if (! $template) {
            throw new \RuntimeException("Template '$templateName' not declared.");
        }

        return $template;
    }
}
