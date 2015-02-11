<?php

namespace Aztech\Phinject\Tests;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Aztech\Phinject\ContainerFactory;
class MethodInvocationTest extends \PHPUnit_Framework_TestCase
{

    public function testGlobalInjectionsAreApplied()
    {
        $yaml = <<<YML
global:
    injections:
        \Psr\Log\LoggerAwareInterface:
            setLogger: [ @logger ]

classes:
    logger:
        class: \Psr\Log\NullLogger
    loggerAware:
        class: \Aztech\Phinject\Tests\LoggerAwareDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $logger = $container->get('logger');
        $loggerAware = $container->get('loggerAware');

        $this->assertSame($logger, $loggerAware->getLogger());
    }

    public function testGlobalInjectionsWithInlineSyntaxAreApplied()
    {
        $yaml = <<<YML
global:
    injections:
        \Psr\Log\LoggerAwareInterface:
            - setLogger(@logger)

classes:
    logger:
        class: \Psr\Log\NullLogger
    loggerAware:
        class: \Aztech\Phinject\Tests\LoggerAwareDummy
YML;

        $container = ContainerFactory::createFromInlineYaml($yaml);

        $logger = $container->get('logger');
        $loggerAware = $container->get('loggerAware');

        $this->assertSame($logger, $loggerAware->getLogger());
    }

}

class LoggerAwareDummy implements LoggerAwareInterface
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}