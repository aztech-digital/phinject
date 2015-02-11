<?php

namespace Aztech\Phinject\Tests\Console;

use Aztech\Phinject\Console\CommandLogger;
use Prophecy\Argument;
use Psr\Log\LogLevel;

class CommandLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogWithThresholdWritesMessagesWhenThresholdExceeded()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::containingString('info-message'))->shouldBeCalled();
        $output->writeln(Argument::containingString('error-message'))->shouldBeCalled();

        $logger = new CommandLogger($output->reveal());
        $logger->enableThreshold(LogLevel::ERROR);

        $logger->info('info-message');
        $logger->error('error-message');
    }

    public function testLogWithThresholdDoesWriteMessagesWhenThresholdNotExceeded()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::any())->shouldNotBeCalled();

        $logger = new CommandLogger($output->reveal());
        $logger->enableThreshold(LogLevel::CRITICAL);

        $logger->info('info-message');
        $logger->error('error-message');
    }
}