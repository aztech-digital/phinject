<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Util\ArrayResolver;
use Interop\Container\ContainerInterface;

class ObjectContainer implements Container, DelegatingContainer
{

    /**
     *
     * @var ArrayResolver
     */
    protected $config = null;

    /**
     *
     * @var ParameterContainer
     */
    protected $parameterContainer;

    /**
     *
     * @var ArrayResolver
     */
    protected $classes;

    /**
     *
     * @var ObjectRegistry
     */
    protected $registry = null;

    /**
     *
     * @var ServiceBuilder
     */
    protected $serviceBuilder;

    /**
     *
     * @var ReferenceResolver
     */
    protected $referenceResolver = null;

    /**
     *
     * @var ContainerInterface
     */
    protected $delegateContainer = null;

    /**
     *
     * @var \SplStack
     */
    protected $buildContext = null;

    /**
     *
     * @param Config $config
     */
    public function __construct(Config $config, ServiceBuilder $builder)
    {
        $this->config = $config->getResolver();

        $this->buildContext = new \SplStack();
        $this->serviceBuilder = $builder;
        $this->classes = $this->config->resolveArray('classes', []);
        $this->registry = new ObjectRegistry();

        $this->setDelegateContainer($this);
    }

    public function setDelegateContainer(ContainerInterface $container)
    {
        $this->delegateContainer = $container;

        $this->referenceResolver = new DefaultReferenceResolver($this->delegateContainer, $this);
        $this->parameterContainer = new ParameterContainer(
            $this,
            $this->config->resolveArray('parameters', [])
        );
    }

    public function build($definition, $serviceName = null)
    {
        if ($serviceName === null) {
            $serviceName = md5(var_export($definition, true) . rand());
        }

        if (! $definition instanceof ArrayResolver) {
            $definition = new ArrayResolver($definition);
        }

        return $this->serviceBuilder->buildService($this, $definition, $serviceName);
    }

    public function getBuildContextName()
    {
        if ($this->buildContext->count()) {
            return $this->buildContext->top();
        }

        throw new \RuntimeException('No current build context');
    }

    /**
     * Binds an existing object or an object definition to a key in the container.
     *
     * @param string $key The key to which the new object/definition should be bound.
     * @param mixed $item An array or an object to bind.
     *        If $item is an object, it will be registered as a singleton in the
     *        object registry. Otherwise, $item will be handled as an object definition.
     */
    public function bind($key, $item)
    {
        if (is_array($item)) {
            return $this->classes[$key] = $item;
        }

        return $this->registry->set($key, $item);
    }

    /**
     * Binds by reference an existing object or an object definition to a key in the container.
     *
     * @param string $key The key to which the new object/definition should be bound.
     * @param mixed $item An array or an object to bind.
     *        If $item is an object, it will be registered as a singleton in the
     *        object registry. Otherwise, $item will be handled as an object definition.
     */
    public function lateBind($key, & $item)
    {
        if (is_array($item)) {
            return $this->classes[$key] = $item;
        }

        return $this->registry->rset($key, $item);
    }

    /**
     * Set a parameter in the container on any key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParameter($key, $value)
    {
        $this->parameterContainer->set($key, $value);

        return $this;
    }

    /**
     * Retrieve the parameter value configured in the container
     *
     * @param string $parameterName
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        return $this->parameterContainer->get($parameterName);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Phinject\Container::hasParameter()
     */
    public function hasParameter($parameterName)
    {
        return $this->parameterContainer->has($parameterName);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Phinject\Container::has()
     */
    public function has($serviceName)
    {
        if ($this->classes->resolve($serviceName, false) !== false) {
            return true;
        }

        return $this->registry->has($serviceName);
    }

    /**
     * Retrieve a class configured in the container
     *
     * @param string $serviceName
     * @return object @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function get($serviceName)
    {
        if ($this->registry->has($serviceName)) {
            $service = $this->registry->getStrict($serviceName);
        }
        elseif ($this->parameterContainer->has($serviceName)) {
            $service = $this->parameterContainer->get($serviceName);
        }
        else {
            $service = $this->resolveService($serviceName);
        }

        return $service;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Aztech\Phinject\Container::getNamespace()
     */
    public function getNamespace($namespace)
    {
        $classes = [];
        $definitions = $this->classes->extract();

        foreach (array_keys($definitions) as $class) {
            if (strpos($class, $namespace, 0) === 0) {
                $classes[$class] = $this->get($class);
            }
        }

        return $classes;
    }

    /**
     *
     * @return ArrayResolver
     */
    public function getGlobalConfig()
    {
        return $this->config;
    }

    /**
     * Resolves the value of a reference key.
     *
     * @param string $reference
     * @return mixed
     */
    public function resolve($reference)
    {
        return $this->referenceResolver->resolve($reference);
    }

    /**
     * Resolves an array of references.
     *
     * @param array $references
     * @return array containing all the resolved references
     */
    public function resolveMany(array $references)
    {
        return $this->referenceResolver->resolveMany($references);
    }

    /**
     * Flush the registry
     *
     * @return Container
     */
    public function flushRegistry()
    {
        $this->registry->flush();

        return $this;
    }

    /**
     *
     * @param string $serviceName
     */
    protected function resolveService($serviceName)
    {
        $serviceConfig = $this->classes->resolve($serviceName, false);

        if ($serviceConfig == false) {
            throw new UnknownDefinitionException($serviceName);
        }

        try {
            $this->buildContext->push($serviceName);
            $service = $this->loadService($serviceName, $serviceConfig);
            $this->buildContext->pop();
        } catch (UnknownDefinitionException $ex) {
            $this->buildContext->pop();
            throw new UnknownDefinitionException(sprintf("Dependency '%s' not found while trying to build '%s'.", $ex->getServiceName(), $serviceName));
        }

        return $service;
    }

    /**
     * Chain of command of the class loader
     *
     * @param ArrayResolver $serviceConfig
     * @param string $serviceName
     * @return object
     */
    protected function loadService($serviceName, $serviceConfig)
    {
        return $this->serviceBuilder->buildService($this, $serviceConfig, $serviceName);
    }
}
