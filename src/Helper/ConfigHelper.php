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

    public $filePathToThings = '/Users/jrossdeutscher/workspace/imagelib2/Ereignisse2';
    public $filePathToCache = '/Users/jrossdeutscher/workspace/imagelib2/Cache/Ereignisse2';
    public $filePathToRepo = '/Users/jrossdeutscher/workspace/imagelib2/Ereignisse2.repo';

    #Lowercase!
    public $fileExtensionsOfBasicImages = array('jpg', 'jpeg');
    public $fileExtensionsOfRawImages = array('dng', 'raw');
    public $fileExtensionsOfImages ;

    public $fileExtensionsOfBasicMovies = array('mp4');
    public $fileExtensionsOfProprietaryMovies = array('mov');
    public $fileExtensionsOfMovies ;

    public $fileExtensionsOfThings;

    public $derivedFiles=array(
        'thumbnail'=>array(
            'width'=>200,
            'height'=>150,
        ),
        'poster'=>array(
            'width'=>1200,
            'height'=>900,
        ),
    );

    public function __construct()
    {
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
    }

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->fileHelper = $thingsManager->getFileHelper();
    }
}
