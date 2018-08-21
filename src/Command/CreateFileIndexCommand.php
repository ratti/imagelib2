<?php

namespace App\Command;

use App\Controller\ThingsController;
use App\Manager\ThingsManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateFileIndexCommand extends Command
{
    protected static $defaultName = 'app:create-file-index';

    public $thingsManager;
    public $thingsController;

    public $configHelper;
    public $fileHelper;

    public function __construct(ThingsManager $thingsManager, ThingsController $thingsController)
    {
        $this->thingsController=$thingsController;
        $thingsController->__construct($thingsManager);
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('creates a repo file from the image dir')
#            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')#            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        #       $arg1 = $input->getArgument('arg1');

        #       if ($arg1) {
        #           $io->note(sprintf('You passed an argument: %s', $arg1));
        #       }

        #       if ($input->getOption('option1')) {
        #           // ...
        #       }

        #       $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $this->thingsController->createFileIndexAction();
        #print_r($thingsRepo->things);


    }
}
