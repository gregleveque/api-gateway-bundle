<?php

namespace Gie\GatewayBundle\Command;

use Gie\Gateway\Cache\CacheManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Cache\CacheItem;

class GatewayExecuteCommand extends Command
{
    /** @var CacheManager  */
    protected $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;

        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setName('api-proxy:execute')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
    }

}
