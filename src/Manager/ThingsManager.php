<?php
/**
 * Class ThingsManager
 *
 */

namespace App\Manager;


use App\Helper\ConfigHelper;
use App\Helper\FileHelper;
use App\Helper\MysqlHelper;
use App\Repository\ThingsRepository;
use App\Repository\ThingsRepositoryDb;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThingsManager
{
    private $logger;
    private $configHelper;
    private $fileHelper;
    private $thingsRepository;
    private $mysqlHelper;

    /** @var  InputInterface $inputInterface */
    public $inputInterface;
    /** @var  OutputInterface $outputInterface */
    public $outputInterface;


    public function __construct(LoggerInterface $logger, ConfigHelper $configHelper, FileHelper $fileHelper, ThingsRepository $thingsRepository, MysqlHelper $mysqlHelper)
    {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->fileHelper = $fileHelper;
        $this->thingsRepository = $thingsRepository;
        $this->mysqlHelper=$mysqlHelper;

        $configHelper->init($this);
        $fileHelper->init($this);
        $mysqlHelper->init($this);
    }

    public function getEmptyRepository()
    {
        $repo = clone $this->thingsRepository;
        $repo->init($this);
        return $repo;
    }

    public function loadRepository($fileName)
    {
        // loading the repo will load the old config, so we overwrite it with the current one.
        $currentConfig = clone $this->configHelper;
        $this->thingsRepository = unserialize(file_get_contents($fileName));
        $this->thingsRepository->configHelper = $currentConfig;

        return $this->thingsRepository;
    }

    public function initBlankRepositoryDb()
    {
        $currentConfig = clone $this->configHelper;
        $this->thingsRepository = new ThingsRepositoryDb();
        $this->thingsRepository->configHelper = $currentConfig;

        return $this->thingsRepository;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return ConfigHelper
     */
    public function getConfigHelper(): ConfigHelper
    {
        return $this->configHelper;
    }

    /**
     * @return FileHelper
     */
    public function getFileHelper(): FileHelper
    {
        return $this->fileHelper;
    }

    /**
     * @return MysqlHelper
     */
    public function getMysqlHelper(): MysqlHelper
    {
        return $this->mysqlHelper;
    }

}
