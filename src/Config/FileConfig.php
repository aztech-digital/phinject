<?php

namespace Aztech\Phinject\Config;

use Aztech\Phinject\Util\ArrayResolver;

class FileConfig extends AbstractConfig
{

    private $parser;

    private $sourceFile;

    public function __construct(Parser $parser, $sourceFile)
    {
        if (! file_exists($sourceFile)) {
            throw new \InvalidArgumentException('Configuration file \'' . $sourceFile . '\' not found.');
        }

        $this->sourceFile = $sourceFile;
        $this->parser = $parser;
    }

    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     *
     * @param string $filePath
     * @throws InvalidConfigurationException
     * @return array
     */
    protected function loadFile($filePath)
    {
        $data = file_get_contents($filePath);

        try {
            $data = $this->parser->parse($data);
        }
        catch (\Exception $ex) {
            throw new InvalidConfigurationException('Invalid file : ' . $filePath);
        }

        return $data;
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Phinject\Config\AbstractConfig::doLoad()
     */
    protected function doLoad()
    {
        $data = $this->getSourcedData($this->sourceFile);
        $root = dirname($this->sourceFile) . DIRECTORY_SEPARATOR;
        $includes = $data->resolve('include', []);

        foreach ($includes as $relativeFilePath) {
            $include = $this->getSourcedData($root . $relativeFilePath);
            $data = $data->mergeRecursiveUnique($include);
        }

        return $data->extract();
    }

    /**
     *
     * @param string $file
     * @throws InvalidConfigurationException
     * @return ArrayResolver
     */
    private function getSourcedData($file)
    {
        $data = $this->objectToArray($this->loadFile($file));

        if (! is_array($data)) {
            throw new InvalidConfigurationException('Invalid configuration, data is not an array in ' . $file);
        }

        return new ArrayResolver($data);
    }

    /**
     *
     * @param object|mixed $mixed
     * @return array|mixed
     */
    private function objectToArray($mixed)
    {
        if (! is_object($mixed)) {
            return $mixed;
        }

        $array = array();

        foreach ($mixed as $property => $value) {
            $array[$property] = $this->objectToArray($value);
        }

        return $array;
    }
}
