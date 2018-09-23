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
use App\Helper\ConfigHelper;
use App\Helper\FileHelper;
use App\Helper\MysqlHelper;
use App\Manager\ThingsManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThingsRepositoryDb extends ThingsRepository
{

    public function getFolderById($folderId): FolderEntity
    {
        $sql = "SELECT * FROM folders WHERE id={$folderId} LIMIT 1";
        $row = $this->thingsManager->getMysqlHelper()->getOneAssocArray($sql);
        if(! count($row)) die("Missing folder $folderId");
        return unserialize($row['payload']);
    }

    public function getThingById($thingId): ThingEntity
    {
        $sql = "SELECT * FROM things WHERE id={$thingId} LIMIT 1";
        $row = $this->thingsManager->getMysqlHelper()->getOneAssocArray($sql);
        return unserialize($row['payload']);
    }

    public function getThingsByThingIds($thingIds): array
    {
        $ret = [];
        $list = join(',', $thingIds);
        $sql = "SELECT * FROM things WHERE id IN ({$list})";
        $rows = $this->thingsManager->getMysqlHelper()->getAllAssocArray($sql);
        foreach ($rows as $row) {
            $ret[$row['id']] = unserialize($row['payload']);
        }
        return $ret;
    }

    public function getFoldersByFolderIds($folderIds): array
    {
        $ret = [];
        $list = join(',', $folderIds);
        $sql = "SELECT * FROM folders WHERE id IN ({$list})";
        $folders = $this->thingsManager->getMysqlHelper()->getAllAssocArray($sql);
        foreach ($folders as $folder) {
            $ret[$folder['id']] = unserialize($folder['payload']);
        }
        return $ret;
    }

    public function getThingsByFolderId($folderId)
    {
        $folder = $this->getFolderById($folderId);
        return $this->getThingsByThingIds($folder->thingsIds);
    }

}
