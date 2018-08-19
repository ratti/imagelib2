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
use App\Entity\ThingEntity;
use App\Manager\ThingsManager;

class ThingsRepository
{
    /* @var \App\Manager\ThingsManager $thingsManager */
    public $thingsManager;

    /* @var $things ThingEntity[] */
    public $things = array();

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $this->logger = $thingsManager->getLogger();
        $this->configHelper = $thingsManager->getConfigHelper();
        $this->fileHelper = $thingsManager->getFileHelper();
    }

    public function initFromFileSystem($baseDir)
    {
        /* @var $files FileEntity[] */
        $files = $this->thingsManager->getFileHelper()->find($baseDir);
        foreach ($files as $file) {
            $this->things[] = new ThingEntity($file);
        }
    }


}
