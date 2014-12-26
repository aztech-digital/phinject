<?php

namespace Aztech\Phinject\Util;

class Arrays
{

    public static function mergeRecursiveUnique(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            self::mergeKeyValuePair($merged, $key, $value);
        }

        return $merged;
    }

    public static function mergeKeyValuePair(array & $merged, $key, $value)
    {
        if (self::isMergeable($merged, $key, $value)) {
            return $merged[$key] = self::mergeRecursiveUnique($merged[$key], $value);
        }

        return $merged[$key] = $value;
    }

    public static function isMergeable($merged, $key, $value)
    {
        return (is_array($value) && isset($merged[$key]) && is_array($merged[$key]));
    }
}
