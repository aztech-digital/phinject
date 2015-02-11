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
use Aztech\Phinject\Resolver\Resolver;
use Aztech\Phinject\Resolver\ServiceResolver;
use Interop\Container\ContainerInterface;

class DefaultReferenceResolver implements ReferenceResolver
{

    const CONTAINER_REGEXP = '`^\$container$`i';

    const ENVIRONMENT_REGEXP = '`^\$env\.(.*)$`i';

    const CONSTANT_REGEXP = '`^\$const\.(.*)$`i';

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var Container
     */
    private $mainContainer;

    /**
     *
     * @var Resolver[]
     */
    private $resolvers = array();

    /**
     * Create a new resolver.
     *
     * @param Container $container
     */
    public function __construct(ContainerInterface $container, Container $mainContainer = null)
    {
        $this->container = $container;

        if (! $mainContainer && $container instanceof Container) {
            $mainContainer = $container;
        }

        if (! $mainContainer) {
            throw new \InvalidArgumentException('Main container is required.');
        }

        $this->mainContainer = $mainContainer;

        $this->resolvers[] = new NullCoalescingResolver($mainContainer);
        $this->resolvers[] = new DynamicParameterResolver($this);
        $this->resolvers[] = new DeferredMethodResolver($mainContainer);
        $this->resolvers[] = new NamespaceResolver($mainContainer);
        $this->resolvers[] = new ParameterResolver($mainContainer);

        $this->resolvers[] = new ServiceResolver($container);

        $this->resolvers[] = new ContainerResolver($mainContainer, self::CONTAINER_REGEXP);
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
            return $this->resolveAnonymousReference($reference);
        }

        return $this->resolveInternal($reference);
    }

    private function isResolvableAnonymousReference($reference)
    {
        return (is_object($reference) || is_array($reference));
    }

    private function resolveAnonymousReference($reference)
    {
        try {
            return $this->mainContainer->build($reference);
        }
        catch (\Exception $ex) {
            if (! isset($reference['isClass']) || ! $reference['isClass']) {
                return $reference;
            }

            throw new UnbuildableServiceException('Anonymous reference could not be built.', 0, $ex);
        }
    }

    /**
     * @param string $reference
     * @return mixed
     */
    private function resolveInternal($reference)
    {
        foreach ($this->resolvers as $resolver) {
            if (is_scalar($reference) && $resolver->accepts($reference)) {
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
