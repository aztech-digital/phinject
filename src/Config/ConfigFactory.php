<?php

namespace Aztech\Phinject\Config;

class ConfigFactory
{

    /**
     * @param string $path
     */
    public static function fromFile($path, $disableGenerated = false)
    {
        if (! file_exists($path) && (! $disableGenerated  && ! file_exists($path . '.phin'))) {
            throw new \InvalidArgumentException("File not found : " . $path);
        }

        if (file_exists($path . '.phin') && ! $disableGenerated) {
            $path .= '.phin';
        }

        $parser = self::getParserFromExtension($path);

        return new FileConfig($parser, $path);
    }

    private static function getParserFromExtension($path)
    {
        $extension = substr($path, strrpos($path, '.') + 1);

        $types = array(
            'phin' => '\Aztech\Phinject\Config\Parser\PhpParser',
            'json' => '\Aztech\Phinject\Config\Parser\JsonParser',
            'php' => '\Aztech\Phinject\Config\Parser\PhpParser',
            'yml' => '\Aztech\Phinject\Config\Parser\YamlParser',
            'yaml' => '\Aztech\Phinject\Config\Parser\YamlParser'
        );

        if (array_key_exists($extension, $types)) {
            return new $types[$extension]($path);
        }

        throw new \InvalidArgumentException('Unable to detect file type : ' . $path);
    }
}
