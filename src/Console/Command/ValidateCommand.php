<?php

namespace Aztech\Phinject\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Aztech\Phinject\Validation\Validator;
use Psr\Log\LogLevel;
use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\Validation\EmptyNodeValidator;
use Aztech\Phinject\Validation\DependencyValidator;
use Aztech\Phinject\Validation\ConstructorArgumentsValidator;
use Aztech\Phinject\Validation\CyclicDependencyValidator;

class ValidateCommand extends Command
{

    protected function configure()
    {
        $this->setName('validate')
            ->setDescription('Validates a dependency injection configuration file')
            ->addArgument('config-file', InputArgument::REQUIRED, 'Source configuration file')
            ->addOption('ignore', null, InputOption::VALUE_OPTIONAL, 'List of ignored service names.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $ignores = explode(',', $input->getOption('ignore'));

        $logger = $this->buildLogger($output, LogLevel::WARNING);
        $config = ConfigFactory::fromFile($file);

        $output->writeln(PHP_EOL . 'Parsing YAML configuration file : ' . $file . PHP_EOL);

        $validator = $this->buildValidator($config, $logger, $ignores);
        $validator->validate();

        $output->writeln(PHP_EOL . 'Done.');
    }

    private function buildValidator($config, $logger, $ignores)
    {
        $validator = new Validator($config, $logger);
        $validator->onNodeInspected(function () use($logger)
        {
            $logger->resetStack();
        });

        $validator->add(new EmptyNodeValidator());

        $dependencyValidator = new DependencyValidator();
        foreach ($ignores as $ignore) {
            $dependencyValidator->ignore($ignore);
        }

        $validator->add($dependencyValidator);
        $validator->add(new ConstructorArgumentsValidator());
        $validator->add(new CyclicDependencyValidator());

        return $validator;
    }
}
