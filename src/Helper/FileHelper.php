<?php
/**
 * Class FileHelper
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Helpers
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Helper;


use App\Entity\FileEntity;
use App\Entity\FolderEntity;
use App\Exception\ThingsException;
use App\Manager\ThingsManager;

class FileHelper
{
    /* @var \App\Manager\ThingsManager */
    public $thingsManager;

    private $logger;
    private $configHelper;

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;

        $this->logger = $thingsManager->getLogger();
        $this->configHelper = $thingsManager->getConfigHelper();
    }

    public function findThings($baseDir)
    {
        $results = $this->findFiles($baseDir);
        foreach ($results as $result) {
            if (strlen($result) && $this->hasThingExtension($result)) {
                $result = substr($result, mb_strlen($baseDir) + 1);
                if (strcmp($result[0], '.') !== 0) {
                    $file = new FileEntity($this->thingsManager, $baseDir, $result);
                    $ret[] = $file;
                }
            }
        }
        return $ret;
    }

    public function findFolders($baseDir)
    {
        $results = $this->findDirs($baseDir);
        foreach ($results as $result) {
            $result = substr($result, mb_strlen($baseDir) + 1);
            if($result===false) $result=''; # Funny enough, root dir ''.
            if (!strlen($result) || strcmp($result[0], '.') !== 0) {
                $folder = new FolderEntity($this->thingsManager, $baseDir, $result);
                $ret[] = $folder;
            }
        }
        return $ret;
    }

    public function findFiles($baseDir)
    {
        if(! is_dir($baseDir)){throw new ThingsException();}

        $escapeBaseDir = escapeshellarg($baseDir);
        $cmd = "find -L $escapeBaseDir -type f -print0";
        $results = explode("\x0", `$cmd`);
        if (!strlen($results[count($results) - 1])) unset($results[count($results) - 1]);
        return $results;
    }

    public function findDirs($baseDir)
    {
        if(! is_dir($baseDir)){throw new ThingsException();}

        $escapeBaseDir = escapeshellarg($baseDir);
        $cmd = "find -L $escapeBaseDir -type d -print0";
        $results = explode("\x0", `$cmd`);
        if (!strlen($results[count($results) - 1])) unset($results[count($results) - 1]);
        sort($results); # Important for building recursive tree: Children after parent!
        return $results;
    }

    public function hasThingExtension($fileName)
    {
        $allowedExtensions = $this->configHelper->fileExtensionsOfThings;
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return false !== array_search(strtolower($extension), $allowedExtensions);
    }
}
