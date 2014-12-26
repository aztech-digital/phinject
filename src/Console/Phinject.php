<?php

namespace Aztech\Phinject\Console;

use Aztech\Phinject\Console\Command\CompileCommand;
use Aztech\Phinject\Console\Command\TestCommand;
use Aztech\Phinject\Console\Command\ValidateCommand;
use Symfony\Component\Console\Application;

class Phinject extends Application
{
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new CompileCommand();
        $commands[] = new TestCommand();
        $commands[] = new ValidateCommand();

        return $commands;
    }
}
