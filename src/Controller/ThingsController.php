<?php
/**
 * Class ThingController
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Controller
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Controller;


use App\Manager\ThingsManager;

class ThingsController
{
    public $thingsManager;
    public $configHelper;
    public $fileHelper;

    public function __construct(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->configHelper = $thingsManager->getConfigHelper();
        $this->fileHelper = $thingsManager->getFileHelper();

    }

    public function createFileIndexAction()
    {
        $baseDir = $this->configHelper->filePathToThings;
        $thingsRepo = $this->thingsManager->getEmptyRepository();
        $thingsRepo->initFromFileSystem($baseDir);
        $thingsRepo->save();
    }

    public function createDerivedFilesAction()
    {
        $fileName = $this->configHelper->filePathToRepo;
        $thingsRepo = $this->thingsManager->loadRepository($fileName);
        foreach($thingsRepo->getAll() as $thing){
            $thing->createAllDerivedFiles();
        }

    }

}
