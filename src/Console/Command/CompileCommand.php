<?php

namespace Aztech\Phinject\Console\Command;

use Aztech\Phinject\Config\ConfigFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCommand extends Command
{
    protected function configure()
    {
        $this->setName('compile')
            ->setDescription('Compile a dependency injection configuration file for production use.')
            ->addArgument('config-file', InputArgument::REQUIRED, 'Source configuration file');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $infile = $input->getArgument('config-file');
        $outfile = $infile . '.phin';

        $output->writeln('Loading configuration from ' . $infile);

        $config = ConfigFactory::fromFile($infile, true);
        $config->load();

        $output->writeln('Dumping compiled configuration to ' . $outfile);

        if (! file_put_contents($outfile, sprintf('<?php return %s; ', $config->compile()))) {
            throw new \RuntimeException('Unable to compile to file ' . $outfile);
        }
    }
}
