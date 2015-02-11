<?php

namespace Aztech\Phinject\Util;

class MethodNameParser
{

    public static function isMethodInvocation($reference)
    {
        if (self::isStaticInvocation($reference) || self::isInstanceInvocation($reference)) {
            return true;
        }

        return false;
    }

    public static function isStaticInvocation($reference)
    {
        return strpos($reference, '::') > 0 && strpos($reference, '::') < strlen($reference) - 1;
    }

    public static function isInstanceInvocation($reference)
    {
        return strpos($reference, '->') > 0 && strpos($reference, '->') < strlen($reference) - 1;
    }

    public static function parseInvocation($service, $methodName, ArrayResolver $parameters) {
        if (is_int($methodName)) {
            return self::getFunctionInvocation($parameters[0]);
        }

        return new MethodInvocationDefinition($service, $methodName, false, $parameters->extract());
    }

    public static function getMethodInvocation($reference)
    {
        if (! self::isMethodInvocation($reference)) {
            throw new \InvalidArgumentException('Reference is not a method invocation reference.');
        }

        $static = (strpos($reference, '::') !== false);
        $args = null;

        list($owner, $name) = explode($static ? '::' : '->', $reference);

        if (self::nameContainsArgs($name)) {
            $args = self::extractArgs($name);
            $name = substr($name, 0, strpos($name, '('));
        }

        return new MethodInvocationDefinition($owner, $name, $static, $args);
    }

    public static function getFunctionInvocation($reference)
    {
        $args = [];

        if (self::nameContainsArgs($reference)) {
            $args = self::extractArgs($reference);
            $name = substr($reference, 0, strpos($reference, '('));
        }

        return new MethodInvocationDefinition(null, $name, false, $args);
    }

    private static function nameContainsArgs($name)
    {
        return strpos($name, '(') > 0 && strpos($name, '(') < strpos($name, ')');
    }

    private static function extractArgs($name)
    {
        $argList = str_replace(' ', '', substr($name, strpos($name, '(') + 1, -1));
        $args = strlen($argList) > 0 ? array($argList) : array();

        if (strlen(trim($argList)) > 0 && strpos($argList, ',') !== false) {
            $args = explode(',', $argList);
        }

        return $args;
    }
}
