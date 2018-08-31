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

        $this->filePathToThings = __DIR__ . '/../../../Ereignisse2';
        $this->filePathToCache = __DIR__ . '/../../../Cache/Ereignisse2';
        $this->filePathToRepo = __DIR__ . '/../../../Ereignisse2.repo';

        #Lowercase!
        $this->fileExtensionsOfBasicImages = array('jpg', 'jpeg');
        $this->fileExtensionsOfRawImages = array('dng', 'raw');
        $this->fileExtensionsOfImages;

        $this->fileExtensionsOfBasicMovies = array('mp4');
        $this->fileExtensionsOfProprietaryMovies = array('mov', 'avi', '3gp');
        $this->fileExtensionsOfMovies;

        $this->fileExtensionsOfThings = array_merge(
            $this->fileExtensionsOfBasicImages,
            $this->fileExtensionsOfRawImages,
            $this->fileExtensionsOfBasicMovies,
            $this->fileExtensionsOfProprietaryMovies
        );

        $this->fileExtensionsOfMovies = array_merge(
            $this->fileExtensionsOfBasicMovies,
            $this->fileExtensionsOfProprietaryMovies
        );

        $this->fileExtensionsOfImages = array_merge(
            $this->fileExtensionsOfBasicImages,
            $this->fileExtensionsOfRawImages
        );

        $this->derivedFiles = array(
            'thumbnail' => array(
                'width' => 200,
                'height' => 150,
            ),
            'poster' => array(
                'width' => 1200,
                'height' => 900,
            ),
        );

    }

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->fileHelper = $thingsManager->getFileHelper();
    }
}
