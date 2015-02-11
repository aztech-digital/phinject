<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\Config\TemplatedConfig;
use Aztech\Phinject\Config\YMLInline;
use Aztech\Phinject\Util\ArrayResolver;

/**
 * Factory class to build dependency injection containers from a configuration file.
 *
 * @author thibaud
 */
class ContainerFactory
{

    private static $factory;

    /**
     * Overrides the default service builder factory.
     *
     * @param ServiceBuilderFactory $factory
     */
    public static function setServiceBuilderFactory(ServiceBuilderFactory $factory)
    {
        self::$factory = $factory;
    }

    /**
     * Creates a new container using the given JSON configuration file.
     *
     * @param string $file Path to the JSON configuration file.
     * @param array $options
     *
     * @return DelegatingContainer
     */
    public static function createFromJson($file, array $options = array())
    {
        return self::createInstance($file, $options);
    }

    /**
     * Creates a new container using the given PHP configuration file.
     *
     * @param string $file Path to the PHP configuration file.
     * @param array $options
     *
     * @return DelegatingContainer
     */
    public static function createFromPhp($file, array $options = array())
    {
        return self::createInstance($file, $options);
    }

    /**
     * Creates a new container using the given YAML configuration file.
     *
     * @param string $file Path to the YAML configuration file.
     * @param array $options
     *
     * @return DelegatingContainer
     */
    public static function createFromYaml($file, array $options = array())
    {
        return self::createInstance($file, $options);
    }

    /**
     * Creates a new container using the given Yaml definition.
     *
     * @param string $yaml The inline Yaml definition.
     * @param array $options
     *
     * @return DelegatingContainer
     */
    public static function createFromInlineYaml($yaml, array $options = array())
    {
        return self::createInstance(new YMLInline($yaml), $options);
    }

    /**
     * Creates a new container using the given configuration.
     *
     * @param Config|string $config An instance of Config or the path of a configuration file.
     * @param array $options
     *
     * @return DelegatingContainer
     */
    public static function create($config, array $options = array())
    {
        return self::createInstance($config, $options);
    }

    private static function createInstance($config, array $options)
    {
        $resolver = new ArrayResolver($options);
        $config = self::getConfig($config, $resolver);
        $builderFactory = self::$factory ?: new ServiceBuilderFactory();

        $serviceBuilderConfig = $config->getResolver()
            ->resolve('config', [], true)
            ->merge($resolver);

        return new ObjectContainer($config, $builderFactory->build($serviceBuilderConfig));
    }

    private static function getConfig($config, ArrayResolver $options)
    {
        if (is_string($config)) {
            $config = self::loadConfig($config);
        }

        if (! ($config instanceof Config)) {
            throw new \InvalidArgumentException(
                'Invalid configuration : must be an instance of Config, a config file path, or valid inline PHP.');
        }

        self::applyDecorators($config, $options);

        return $config;
    }

    private static function getFactory()
    {
        if (! self::$factory) {
            self::$factory = new ServiceBuilderFactory();
        }

        return self::$factory;
    }

    private static function applyDecorators(Config & $config, ArrayResolver $options)
    {
        if ((bool) $options->resolve('templates', false)) {
            $config = new TemplatedConfig($config);
        }
    }

    /**
     * @param string $config
     */
    private static function loadConfig($config)
    {
        if (! file_exists($config)) {
            throw new \InvalidArgumentException(sprintf('Configuration file "%s" not found.', $config));
        }

        $factory = new ConfigFactory();

        return $factory->fromFile($config);
    }
}
