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

class CreateDerivedFilesCommand extends Command
{
    protected static $defaultName = 'app:create-derived-files';

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
            ->setDescription('creates derived images')
#            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')#            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->thingsController->createDerivedFilesAction();
    }
}
