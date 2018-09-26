<?php
/**
 * Class FileHelper
 *
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
        $ret = array();
        $results = $this->findDirs($baseDir);
        foreach ($results as $result) {
            $result = substr($result, mb_strlen($baseDir) + 1);
            if ($result === false) $result = ''; # Funny enough, root dir ''.
            if (!strlen($result) || strcmp($result[0], '.') !== 0) {
                $folder = new FolderEntity($this->thingsManager, $baseDir, $result);
                $ret[] = $folder;
            }
        }
        return $ret;
    }

    public function findFiles($baseDir)
    {
        if (!is_dir($baseDir)) throw new ThingsException();

        $find = $this->thingsManager->getConfigHelper()->cmdFind;
        $escapeBaseDir = escapeshellarg($baseDir);
        $escapeblacklistedFoldersRegExp = escapeshellarg($this->thingsManager->getConfigHelper()->blacklistedFoldersRegExp);
        $cmd = "$find -L $escapeBaseDir -regextype posix-extended -type f -not -regex " . $escapeblacklistedFoldersRegExp . " -print0";
        $results = explode("\x0", `$cmd`);
        if (!strlen($results[count($results) - 1])) unset($results[count($results) - 1]);
        return $results;
    }

    public function findDirs($baseDir)
    {
        if (!is_dir($baseDir)) throw new ThingsException();

        $find = $this->thingsManager->getConfigHelper()->cmdFind;
        $escapeBaseDir = escapeshellarg($baseDir);
        $escapeblacklistedFoldersRegExp = escapeshellarg($this->thingsManager->getConfigHelper()->blacklistedFoldersRegExp);
        $cmd = "$find -L $escapeBaseDir -regextype posix-extended -type d -not -regex " . $escapeblacklistedFoldersRegExp . " -print0";
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
