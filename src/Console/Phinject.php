<?php

namespace Aztech\Phinject\Console;

use Aztech\Phinject\Console\Command\CompileCommand;
use Aztech\Phinject\Console\Command\TestCommand;
use Aztech\Phinject\Console\Command\ValidateCommand;
use Symfony\Component\Console\Application;

class Phinject extends Application
{
    public function __construct($name = 'Phinject', $version = 'Unknown')
    {
        parent::__construct($name, $version);

        $commands = [];

        $commands[] = new CompileCommand();
        $commands[] = new TestCommand();
        $commands[] = new ValidateCommand();

        parent::addCommands($commands);
    }
}
