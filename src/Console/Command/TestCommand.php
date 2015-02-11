<?php

namespace Aztech\Phinject\Console\Command;

use Aztech\Phinject\Config\ConfigFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Test a dependency injection configuration file.')
            ->addArgument('config-file', InputArgument::REQUIRED, 'Source configuration file');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $infile = $input->getArgument('config-file');

        $output->writeln('Loading configuration from ' . $infile);

        $config = ConfigFactory::fromFile($infile);
        $config->load();

        $output->writeln('Successfully loaded configuration ! All is ok !');
    }
}
