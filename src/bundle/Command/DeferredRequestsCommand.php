<?php

namespace Gie\GatewayBundle\Command;


use Gie\Gateway\Cache\CacheManager;
use Gie\Gateway\Request\DeferredRequest;
use Gie\Gateway\Request\RequestHash;
use Gie\Gateway\Request\RequestManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeferredRequestsCommand extends Command
{
    /** @var CacheManager  */
    protected $cacheManager;

    /** @var RequestManager  */
    protected $requestManager;

    /** @var SymfonyStyle */
    protected $io;

    public function __construct(CacheManager $cacheManager, RequestManager $requestManager)
    {
        $this->cacheManager = $cacheManager;
        $this->requestManager = $requestManager;

        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setName('gateway:deferred')
            ->setDescription('Manage deferred requests.')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List all deferred requests')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Purge all deferred requests')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Purge all deferred requests')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        if ($input->getOption('list')) {
            $this->listRequests();
        } else if ($input->getOption('purge')) {
            $this->purgeRequests();
        } else if ($input->getOption('test')) {
            $this->testRequests();
        } else {
            $this->processRequests();
        }
        
    }

    private function listRequests()
    {
        if (!$requests = $this->cacheManager->listDeferredRequests()) {
            $this->io->writeln('<comment>No requests in queue.</comment>');
        } else {
            $this->io->title('List of requests in queue:');
            $this->io->table(['Key', 'Path', 'Event'],
                array_reduce($requests, function ($acc, $request) {
                    return array_merge($acc, [[
                        RequestHash::hash($request),
                        $request->getUri()->getHost(),
                        $request->getEvent(),
                    ]]);
                }, []));
        }
    }

    private function purgeRequests()
    {
        $this->io->title('Purge all deferred requests in queue:');
        if ($this->io->confirm('Do you want to purge all requests ?', false)) {
            $requests = $this->cacheManager->getDeferredRequests();
            $this->io->success(count($requests) . ' requests deleted.');
        }
    }

    private function processRequests()
    {
        /** @var DeferredRequest $request */
        foreach ($this->cacheManager->getDeferredRequests() as $request) {
            $this->requestManager->sendRequest($request);
        }
    }

    private function testRequests()
    {
        $this->cacheManager->deferRequest(new DeferredRequest('GET', 'https://www.google.fr'));
    }

}
