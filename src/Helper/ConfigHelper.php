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

    public function __construct()
    {
        require_once __DIR__ .'/../../config.inc.php';
    }

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->fileHelper = $thingsManager->getFileHelper();
    }
}
