<?php

namespace Aztech\Phinject\Resolver;

use Aztech\Phinject\Container;
use Aztech\Phinject\UnknownDefinitionException;

class NullCoalescingResolver implements Resolver
{

    private $resolver = null;

    public function __construct(Container $resolver)
    {
        $this->resolver = $resolver;
    }

    /*
     * (non-PHPdoc) @see \Aztech\Phinject\Resolver\Resolver::accepts()
     */
    public function accepts($reference)
    {
        return count(explode('?:', $reference, 2)) == 2;
    }

    /*
     * (non-PHPdoc) @see \Aztech\Phinject\Resolver\Resolver::resolve()
     */
    public function resolve($reference)
    {
        $parts = explode('?:', $reference, 2);

        for ($index = 0; $index < count($parts); $index++) {
            try {
                return $this->resolver->resolve(trim($parts[$index]));
            }
            catch (UnknownDefinitionException $exception) {
                continue;
            }
        }

        return null;
    }
}
