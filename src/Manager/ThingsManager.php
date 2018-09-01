<?php
/**
 * Class ThingsManager
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Manager
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Manager;


use App\Helper\ConfigHelper;
use App\Helper\FileHelper;
use App\Repository\ThingsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThingsManager
{
    private $logger;
    private $configHelper;
    private $fileHelper;
    private $thingsRepository;

    /** @var  InputInterface $inputInterface */
    public     $inputInterface;
    /** @var  OutputInterface $outputInterface */
    public     $outputInterface;



    public function __construct(LoggerInterface $logger, ConfigHelper $configHelper, FileHelper $fileHelper, ThingsRepository $thingsRepository)
    {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->fileHelper = $fileHelper;
        $this->thingsRepository = $thingsRepository;

        $configHelper->init($this);
        $fileHelper->init($this);
    }

    public function getEmptyRepository()
    {
        $repo = clone $this->thingsRepository;
        $repo->init($this);
        return $repo;
    }

    public function loadRepository($fileName)
    {
        $this->thingsRepository=unserialize(file_get_contents($fileName));
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

}
