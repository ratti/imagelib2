<?php
/**
 * Class ThingsRepository
 */

namespace App\Repository;


use App\Entity\FileEntity;
use App\Entity\FolderEntity;
use App\Entity\ThingEntity;
use App\Helper\ConfigHelper;
use App\Helper\FileHelper;
use App\Helper\MysqlHelper;
use App\Manager\ThingsManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThingsRepository
{
    /* @var \App\Manager\ThingsManager $thingsManager */
    public $thingsManager;

    /* @var $things ThingEntity[] */
    public $things = array();

    /* @var $folders FolderEntity[] */
    public $folders = array();
    public $folderIds = array();

    // Logging helper
    private $logLastRelDirName;

    /* @var ConfigHelper $configHelper */
    public $configHelper;

    /* @var FileHelper $fileHelper */
    public $fileHelper;

    /* @var LoggerInterface $logger*/
    public $logger;

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

#        if (!is_null($folderEntity->parentRelPath)) {
        if ($folderEntity->relPath !== '') {
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

    public function getFolderById($folderId)
    {
        return $this->folders[$folderId];
    }

    public function addThing(ThingEntity $thingEntity)
    {
        $id = count($this->things) + 1;
        $thingEntity->id = $id;
        $this->things[$id] = $thingEntity;

        $relDirName = $thingEntity->masterFile->relDirName;
        $this->logFolder($relDirName);
        $folderId = $this->getFolderByPath($relDirName);
        $this->folders[$folderId]->thingsIds[] = $id;
        $thingEntity->folderId = $folderId;
        return $thingEntity;
    }

    public function logFolder($relDirName)
    {
        /** @var OutputInterface $output */
        $output = $this->thingsManager->outputInterface;
        if ($output && $output->isVerbose()) {
            if ($relDirName != $this->logLastRelDirName) {
                $this->logLastRelDirName = $relDirName;
                $output->writeln("Folder: <info>$relDirName</info>");
            }
        }
    }

    public function save()
    {
        $fileName = $this->thingsManager->getConfigHelper()->filePathToRepo;
        file_put_contents($fileName, serialize($this));
    }

    public function saveDb()
    {
        $db=$this->thingsManager->getMysqlHelper();
        $db->query('TRUNCATE things');
        $db->query('TRUNCATE folders');

        foreach ($this->things as $id => $thing) {
            $db->query('REPLACE INTO things SET id='.$id.' , folderid='.$thing->folderId.', payload="'.$db->quote(serialize($thing)).'"');
        }

        foreach ($this->folders as $id => $folder) {
            $db->query('REPLACE INTO folders SET id='.$id.' , payload="'.$db->quote(serialize($folder)).'"');
        }
    }

    public function getAllThings()
    {
        return $this->things;
    }

    /**
     * @param $id
     * @return ThingEntity
     */
    public function getThingById($id)
    {
        return $this->things[$id];
    }

    public function getThingsByFolderId($id)
    {
        $folder = $this->getFolderById($id);
        $ret = array();
        foreach ($folder->thingsIds as $thingsId) {
            $ret[] = $this->getThingById($thingsId);
        }
        uasort($ret, array($this, 'sortThingsArray'));
        return $ret;
    }

    public function sortThingsArray(ThingEntity $a, ThingEntity $b)
    {
        return $a->masterFile->absFileName <=> $b->masterFile->absFileName;
    }

    public function getFoldersByFolderIds($folderIds)
    {
        $ret = array();
        foreach ($folderIds as $folderId) {
            $ret[] = $this->getFolderById($folderId);
        }
        return $ret;
    }

    public function getAllDerivedFiles($attribute = 'absFileName', $asKey = false)
    {
        $ret = array();
        foreach ($this->things as $thing) {
            if ($asKey) {
                $ret[$thing->posterFile->{$attribute}] = true;
                $ret[$thing->thumbnailFile->{$attribute}] = true;
                $ret[$thing->exifFile->{$attribute}] = true;
                $ret[$thing->derivedVideoFile->{$attribute}] = true;
            } else {
                $ret[] = $thing->posterFile->{$attribute};
                $ret[] = $thing->thumbnailFile->{$attribute};
                $ret[] = $thing->exifFile->{$attribute};
                $ret[] = $thing->derivedVideoFile->{$attribute};
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
