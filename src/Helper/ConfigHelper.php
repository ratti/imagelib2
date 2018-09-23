<?php
/**
 * Class ConfigHelper
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


use App\Manager\ThingsManager;

class ConfigHelper
{
    public $thingsManager;

    private $logger;
    private $fileHelper;

    public $filePathToThings;
    public $filePathToCache;
    public $filePathToRepo;

    public $fileExtensionsOfBasicImages;
    public $fileExtensionsOfRawImages;
    public $fileExtensionsOfImages;

    public $fileExtensionsOfBasicMovies;
    public $fileExtensionsOfProprietaryMovies;
    public $fileExtensionsOfMovies;

    public $fileExtensionsOfThings;

    public $derivedFiles;

    public $blacklistedFoldersRegExp;

    public $webroot;

    public $mysqlUser;
    public $mysqlPassword;
    public $mysqlHost;
    public $mysqlDatabase;

    public function __construct()
    {
        require_once __DIR__ . '/../../config.inc.php';
        $this->webRoot = __DIR__ . '/../../public';

        if (!file_exists("{$this->filePathToCache}")) mkdir($this->filePathToCache, 0777, true);
        if (!file_exists("{$this->filePathToCache}/poster")) mkdir("{$this->filePathToCache}/poster", 0777, true);
        if (!file_exists("{$this->filePathToCache}/thumbnail")) mkdir("{$this->filePathToCache}/thumbnail", 0777, true);
        if (!file_exists("{$this->filePathToCache}/video")) mkdir("{$this->filePathToCache}/video", 0777, true);
        if (!file_exists("{$this->filePathToCache}/exif")) mkdir("{$this->filePathToCache}/exif", 0777, true);

        if (!file_exists("{$this->webRoot}/master")) symlink($this->filePathToThings, "{$this->webRoot}/master");
        if (!file_exists("{$this->webRoot}/poster")) symlink("$this->filePathToCache/poster", "{$this->webRoot}/poster");
        if (!file_exists("{$this->webRoot}/thumbnail")) symlink("$this->filePathToCache/thumbnail", "{$this->webRoot}/thumbnail");
        if (!file_exists("{$this->webRoot}/video")) symlink("$this->filePathToCache/video", "{$this->webRoot}/video");
    }

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->fileHelper = $thingsManager->getFileHelper();
    }
}
