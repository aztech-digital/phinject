<?php

namespace Aztech\Phinject\Initializer;

use Aztech\Phinject\Container;
use Aztech\Phinject\TypeInitializer;
use Aztech\Phinject\Injectors\MethodInvoker;
use Aztech\Phinject\Util\ArrayResolver;
use Aztech\Phinject\Util\MethodNameParser;

class OnceTypeInitializer implements TypeInitializer
{

    private static $invoked = [];

    private $parser;

    private $invoker;

    public function __construct()
    {
        $this->parser = new MethodNameParser();
        $this->invoker = new MethodInvoker();
    }

    public function initialize(Container $container, ArrayResolver $serviceConfig)
    {
        $bootstrap = $serviceConfig->resolveArray('before.once', []);

        foreach ($bootstrap as $invocation) {
            if (! $this->shouldInvoke($invocation)) {
                continue;
            }

            $method = $this->parser->getMethodInvocation($invocation);

            $this->invoker->invokeStatic($container, $method);

            self::$invoked[] = $invocation;
        }
    }

    private function shouldInvoke($invocation)
    {
        if (! $this->parser->isStaticInvocation($invocation)) {
            return false;
        }

        return ! $this->wasCalled($invocation);
    }

    private function wasCalled($definition)
    {
        return in_array($definition, self::$invoked, true);
    }
}
