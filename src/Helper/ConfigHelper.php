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

    #Lowercase!
    public $fileExtensionsOfBasicImages = array('jpg', 'jpeg');
    public $fileExtensionsOfRawImages = array('dng', 'raw');
    public $fileExtensionsOfBasicMovies = array('mp4');
    public $fileExtensionsOfProprietaryMovies = array('mov');
    public $fileExtensionsOfThings;

    public function __construct()
    {
        $this->fileExtensionsOfThings = array_merge(
            $this->fileExtensionsOfBasicImages,
            $this->fileExtensionsOfRawImages,
            $this->fileExtensionsOfBasicMovies,
            $this->fileExtensionsOfProprietaryMovies
        );
    }

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->fileHelper = $thingsManager->getFileHelper();
    }
}
