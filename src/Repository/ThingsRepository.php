<?php
/**
 * Class ThingsRepository
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Repositories
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Repository;


use App\Entity\FileEntity;
use App\Entity\FolderEntity;
use App\Entity\ThingEntity;
use App\Manager\ThingsManager;

class ThingsRepository
{
    /* @var \App\Manager\ThingsManager $thingsManager */
    public $thingsManager;

    /* @var $things ThingEntity[] */
    public $things = array();

    /* @var $folders FolderEntity[] */
    public $folders = array();
    public $folderIds = array();

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->configHelper = $thingsManager->getConfigHelper();
        $this->fileHelper = $thingsManager->getFileHelper();
    }

    public function initFromFileSystem($baseDir)
    {
        /* @var $olders FolderEntity[] */
        $folders = $this->thingsManager->getFileHelper()->findFolders($baseDir);
        foreach ($folders as $folderEntity) {
            $this->addFolder($folderEntity);
        }

        /* @var $files FileEntity[] */
        $files = $this->thingsManager->getFileHelper()->findThings($baseDir);
        foreach ($files as $file) {
            $thingEntity = $this->addThing(new ThingEntity($this->thingsManager, $file));
#            $folderEntity = $this->registerFolder($thingEntity);
#            $thingEntity->folderId = $folderEntity->id;
#            $folderEntity->thingsIds[] = $thingEntity->id;
        }
    }

    public function registerFolder(ThingEntity $thingEntity)
    {
        $relPath = $thingEntity->masterFile->relDirName;

        if (array_key_exists($relPath, $this->folderIds)) {
            $id = $this->folderIds[$relPath];
            return $this->folders[$id];
        }
        $folderEntity = new FolderEntity($this->thingsManager, $thingEntity->masterFile->baseDir, $relPath);

        $newId = $this->addFolder($folderEntity);
        $this->registerParentFolders($this->folders[$newId]);
        return $this->folders[$newId];
    }

    public function registerParentFolders(FolderEntity $folderEntity)
    {
        $parentRelPath = $folderEntity->parentRelPath;
        if (is_null($parentRelPath)) return; # root
        if (array_key_exists($parentRelPath, $this->folderIds)) return; # folder is already known

        $parentFolderEntity = new FolderEntity($this->thingsManager, $folderEntity->baseDir, $parentRelPath);

        $id = count($this->folders);
        $parentFolderEntity->id = $id;
        $parentFolderEntity->subfolderIds[] = $folderEntity->id;

        $this->folders[$id] = $parentFolderEntity;
        $this->folderIds[$parentRelPath] = $id;
        $this->registerParentFolders($parentFolderEntity);
    }

    public function addFolder(FolderEntity $folderEntity)
    {
        $id = count($this->folders);
        $folderEntity->id = $id;
        $this->folders[$id] = $folderEntity;
        $this->folderIds[$folderEntity->relPath] = $id;

        if (!is_null($folderEntity->parentRelPath)) {
            $parentId = $this->folderIds[$folderEntity->parentRelPath];
            $folderEntity->parentId = $parentId;
            $this->folders[$parentId]->subfolderIds[] = $id;
        }

        return $id;
    }

    public function getFolderByPath($relPath)
    {
        return $this->folderIds[$relPath];
    }

    public function addThing(ThingEntity $thingEntity)
    {
        $id = count($this->things) + 1;
        $thingEntity->id = $id;
        $this->things[$id] = $thingEntity;

        $folderId = $this->getFolderByPath($thingEntity->masterFile->relDirName);
        $this->folders[$folderId]->thingsIds[] = $id;
        $thingEntity->folderId = $id;
        return $thingEntity;
    }

    public function save()
    {
        $fileName = $this->thingsManager->getConfigHelper()->filePathToRepo;
        file_put_contents($fileName, serialize($this));
    }

    public function getAll()
    {
        return $this->things;
    }

    public function getById($id)
    {
        return $this->things[$id];
    }

    public function getAllDerivedFiles($attribute = 'absFileName', $asKey = false)
    {
        $ret = array();
        foreach ($this->things as $thing) {
            if ($asKey) {
                $ret[$thing->posterFile->{$attribute}] = true;
                $ret[$thing->thumbnailFile->{$attribute}] = true;
            } else {
                $ret[] = $thing->posterFile->{$attribute};
                $ret[] = $thing->thumbnailFile->{$attribute};
            }
        }
        return $ret;
    }

    public function dump()
    {
        echo "----------------- FOLDER MAPPING ------------------\n";
        print_r($this->folderIds);

        echo "----------------- FOLDERS ------------------\n";
        foreach ($this->folders as $folder) {
            echo $folder->relPath . "\n";
            $folder->dump();
            echo "\n";
        }

        echo "----------------- THINGS ------------------\n";
        foreach ($this->things as $thing) {
            echo "THING:\n" . $thing->masterFile->relFileName . "\n";
            $thing->dump();
            echo "\n";
        }
    }

}
