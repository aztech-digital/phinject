<?php

namespace Aztech\Phinject;

use Aztech\Phinject\Resolver\ConstantResolver;
use Aztech\Phinject\Resolver\ContainerResolver;
use Aztech\Phinject\Resolver\DeferredMethodResolver;
use Aztech\Phinject\Resolver\DynamicParameterResolver;
use Aztech\Phinject\Resolver\EnvironmentVariableResolver;
use Aztech\Phinject\Resolver\NamespaceResolver;
use Aztech\Phinject\Resolver\NullCoalescingResolver;
use Aztech\Phinject\Resolver\ParameterResolver;
use Aztech\Phinject\Resolver\PassThroughResolver;
use Aztech\Phinject\Resolver\ServiceResolver;
use Interop\Container\ContainerInterface;

class DefaultReferenceResolver implements ReferenceResolver
{

    const CONTAINER_REGEXP = '`^\$container$`i';

    const ENVIRONMENT_REGEXP = '`^\$env\.(.*)$`i';

    const CONSTANT_REGEXP = '`^\$const\.(.*)$`i';

    /**
     *
     * @var Container
     */
    private $container;

    private $resolvers = array();

    /**
     * Create a new resolver.
     *
     * @param Container $container
     */
    public function __construct(ContainerInterface $container, Container $fallback = null)
    {
        $this->container = $container;

        if (! $fallback && $container instanceof Container) {
            $fallback = $container;
        }

        if (! $fallback) {
            throw new \InvalidArgumentException('Fallback container is required.');
        }

        $this->resolvers[] = new NullCoalescingResolver($fallback);
        $this->resolvers[] = new DynamicParameterResolver($this);
        $this->resolvers[] = new DeferredMethodResolver($fallback);
        $this->resolvers[] = new NamespaceResolver($fallback);
        $this->resolvers[] = new ParameterResolver($fallback);

        $this->resolvers[] = new ServiceResolver($container, $fallback);

        $this->resolvers[] = new ContainerResolver($fallback, self::CONTAINER_REGEXP);
        $this->resolvers[] = new EnvironmentVariableResolver(self::ENVIRONMENT_REGEXP);
        $this->resolvers[] = new ConstantResolver(self::CONSTANT_REGEXP);
        $this->resolvers[] = new PassThroughResolver();
    }

    /**
     * Return the resolved value of the given reference.
     *
     * @param mixed $reference
     * @return mixed
     */
    public function resolve($reference)
    {
        if ($this->isResolvableAnonymousReference($reference)) {
            try {
                return $this->container->build($reference);
            }
            catch (\Exception $ex) {
                if (! isset($reference['isClass']) || ! $reference['isClass']) {
                    return $reference;
                }

                throw $ex;
            }
        }

        return $this->resolveInternal($reference);
    }

    private function isResolvableAnonymousReference($reference)
    {
        if (! ($this->container instanceof Container)) {
            return false;
        }

        return is_object($reference) || is_array($reference);
    }

    /**
     * @param string $reference
     * @return mixed
     */
    private function resolveInternal($reference)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->accepts($reference)) {
                return $resolver->resolve($reference);
            }
        }

        throw new UnknownDefinitionException($reference);
    }

    /**
     * Resolves an array of references
     *
     * @param array $references
     * @return mixed
     */
    public function resolveMany(array $references)
    {
        $convertedParameters = array();

        foreach ($references as $reference) {
            $convertedValue = $this->resolve($reference);
            $convertedParameters[] = $convertedValue;
        }

        return $convertedParameters;
    }
}
