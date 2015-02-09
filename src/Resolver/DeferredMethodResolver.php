<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;
use Aztech\Phinject\InvalidReferenceException;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodInvocationDefinition;
use Aztech\Phinject\Util\MethodNameParser;

class DeferredMethodResolver implements Resolver
{

    private $methodNameParser;

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->methodNameParser = new MethodNameParser();
    }

    /**
     * (non-PHPdoc) @see Resolver::accepts()
     */
    public function accepts($reference)
    {
        return substr($reference, 0, 7) == '$defer:';
    }

    /**
     * (non-PHPdoc) @see Resolver::resolve()
     */
    public function resolve($reference)
    {
        $reference = substr($reference, 7);
        $method = $this->getMethod($reference);
        $callback = [
            $this->container->resolve($method->getOwner()),
            $method->getName()
        ];

        if ($method->hasArguments()) {
            return function () use($callback, $method)
            {
                return call_user_func_array($callback, $method->extractArguments($this->container, new ArrayResolver()));
            };
        }

        return is_array($callback) ? function () use ($callback) {
            return call_user_func_array($callback, func_get_args());
        } : $callback;
    }

    /**
     * @param string $reference
     * @throws InvalidReferenceException
     * @return MethodInvocationDefinition
     */
    private function getMethod($reference)
    {
        if (! $this->methodNameParser->isMethodInvocation($reference)) {
            throw new InvalidReferenceException($reference);
        }

        return $this->methodNameParser->getMethodInvocation($reference);
    }
}
