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


use App\Entity\File;

class FileHelper
{
    public $thingsManager;

    public function find($baseDir)
    {
        $ret = array();
        $escapeBaseDir = escapeshellarg($baseDir);
        $cmd = "find -L $escapeBaseDir -type f -print0";

        $results = explode("\x0", `$cmd`);
        foreach ($results as $result) {
            if (strlen($result)) {
                $result = substr($result, mb_strlen($baseDir) + 1);
                if (strcmp($result[0], '.') !== 0) {
                    $file = new File($baseDir, $result);
                    $ret[] = $file;
                }
            }
        }
        print_r($ret);
        return $ret;
    }

    public function hasThingExtension($fileName)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    }
}
