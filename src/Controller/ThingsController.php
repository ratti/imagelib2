<?php
/**
 * Class ThingController
 *
 */

namespace App\Controller;


use App\Entity\ThingEntity;
use App\Manager\ThingsManager;
use App\Repository\ThingsRepository;
use App\Repository\ThingsRepositoryDb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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

#        $this->thingsManager->getMysqlHelper()->;
    }

    public function createNewIndexAction()
    {
        $baseDir = $this->configHelper->filePathToThings;
        /** @var ThingsRepository $thingsRepo */
        $thingsRepo = $this->thingsManager->getEmptyRepository();
        $thingsRepo->initFromFileSystem($baseDir);
        $thingsRepo->save();
        $thingsRepo->saveDb();
    }

    public function createDerivedFilesAction()
    {
        $thingsRepo = $this->loadRepository();
        foreach ($thingsRepo->getAllThings() as $thing) {
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

    public function dumpRepositoryAction()
    {
        $repository = $this->loadRepository();
        $repository->dump();
    }


    /**
     * Matches /folder/*
     *
     * @Route("/folder/{folderId}", name="folder_action")
     * @param int $folderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function folderAction($folderId = 0)
    {
        /* @var ThingsRepositoryDb $thingsRepo */
        $thingsRepo = $this->getBlankRepositoryDb();

        $folder = $thingsRepo->getFolderById($folderId);

        $subfolders = $thingsRepo->getFoldersByFolderIds($folder->subfolderIds);
        foreach ($subfolders as $subfolder) {
            $title = $subfolder->title;
            $title=preg_replace('/^(\d{4})_(\d{2})_(\d{2})__(.*)/uis','\1-\2-\3 \4',$title);
            $title=preg_replace('/_+/uis',' ',$title);
            $subfolder->titleBeautiful = $title;
        }

        return $this->render('folder/show.html.twig', [
            'folder' => $folder,
            'subfolders' => $subfolders,
            'things' => $thingsRepo->getThingsByFolderId($folderId)
        ]);
    }

    /**
     * Matches /download/*
     *
     * @Route("/download/{thingId}", name="download_action")
     */
    public function downloadAction($thingId)
    {

        $thingsRepo = $this->loadRepository();

        /* @var ThingEntity $thing */
        $thing = $thingsRepo->getThingById($thingId);

        return $this->file($thing->masterFile->absFileName);
    }


    /* --------------//--------------//--------------//--------------//-------------- */

    public function loadRepository()
    {
        return $this->thingsManager->loadRepository(
            $this->configHelper->filePathToRepo
        );
    }

    public function getBlankRepositoryDb()
    {
        $repo = $this->thingsManager->initBlankRepositoryDb();
        $repo->init($this->thingsManager);
        return $repo;
    }
}
