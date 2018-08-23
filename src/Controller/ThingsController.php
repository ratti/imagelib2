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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ThingsController extends AbstractController
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
        $thingsRepo=$this->loadRepository();
        foreach ($thingsRepo->getAll() as $thing) {
            $thing->createAllDerivedFiles();
        }
        $thingsRepo->save(); /* exif was created here */
    }

    public function CleanUpDerivedFilesAction()
    {
        $repository = $this->loadRepository();
        $repoFiles = $repository->getAllDerivedFiles('absFileName', true);

        $realFiles = $this->fileHelper->findFiles($this->configHelper->filePathToCache);

        $filesToDelete = array();
        foreach ($realFiles as $realFile) {
            if (!array_key_exists($realFile, $repoFiles)) $filesToDelete[] = $realFile;
        }
        foreach ($filesToDelete as $fileToDelete) {
            unlink($fileToDelete);
        }
    }

    public function dumpRepositoryAction(){
        $repository=$this->loadRepository();
        $repository->dump();
    }

    /* --------------//--------------//--------------//--------------//-------------- */

    public function loadRepository()
    {
        return $this->thingsManager->loadRepository(
            $this->configHelper->filePathToRepo
        );
    }
}
