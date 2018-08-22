<?php
/**
 * Class ThingEnttity
 *
 * This Software is the property of superReal and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.superreal.de
 * @package App\Entities
 * @copyright (C) superReal 2018
 * @author    Joerg Rossdeutscher <j.rossdeutscher _AT_ superreal.de>
 */

namespace App\Entity;

use App\Manager\ThingsManager;

class ThingEntity
{
    public $thingsManager;

    /* @var \App\Entity\FileEntity $masterFile */
    public $masterFile;

    /* @var \App\Entity\FileEntity $thumbnailFile */
    public $thumbnailFile;

    /* @var \App\Entity\FileEntity $posterFile */
    public $posterFile;

    public $masterExif;

    public $folderId;

    public $id;

    public function dump(){

        echo "id: ".$this->id."\n";

        echo "folder id: ".$this->folderId."\n";

        echo "master file:\n";
        $this->masterFile->dump();
        echo "thumbnail file:\n";
        $this->thumbnailFile->dump();
        echo "poster file:\n";
        $this->posterFile->dump();
    }

    public function __construct(ThingsManager $thingsManager, FileEntity $file)
    {
        $this->thingsManager = $thingsManager;

        $this->masterFile = $file;
        $this->thumbnailFile = $this->generatePath('thumbnail');
        $this->posterFile = $this->generatePath('poster');

        $this->setExif();
    }

    public function generatePath($prefix)
    {
        $baseDir = $this->thingsManager->getConfigHelper()->filePathToCache . '/' . $prefix;
        return new FileEntity($this->thingsManager, $baseDir, $this->masterFile->relFileName . '.jpg');
    }

    public function setExif()
    {
        $file = (string)$this->masterFile;
        $cmd = 'exiftool -f -n -j -b ' . escapeshellarg($file);
        $output = `$cmd`;
        $obj = json_decode($output)[0];

        unset($obj->ThumbnailImage);
        unset($obj->OtherImage);

        $this->masterExif = $obj;
    }

    public function getOrientation()
    {
        if (isset($this->masterExif->Orientation)) {
            return $this->masterExif->Orientation;
        } else {
            return 1;
        }
    }


    public function createAllDerivedFiles()
    {
        $this->createDerivedThumbnailFile();
        $this->createDerivedPosterFile();
    }

    public function createDerivedThumbnailFile()
    {
        $this->createDerivedFileAbstract(
            $inFile = $this->masterFile->absFileName,
            $outFile = $this->thumbnailFile->absFileName,
            '256x256'
        );
    }

    public function createDerivedPosterFile()
    {
        $this->createDerivedFileAbstract(
            $inFile = $this->masterFile->absFileName,
            $outFile = $this->posterFile->absFileName,
            '1248x960'
        );
    }

    public function createDerivedFileAbstract($inFile, $outFile, $size)
    {
        $outDir = dirname($outFile);

        if (!is_dir($outDir)) mkdir($outDir, 0777, true);

        if (!file_exists($outFile) || filemtime($inFile) !== filemtime($outFile)) {

            if ($this->isImage()) {
                $cmd = sprintf('convert %s -resize %s^ -auto-orient -gravity center -extent %s  %s',
                    escapeshellarg($inFile), # infile
                    $size, # resize
                    $size, # extend
                    escapeshellarg($outFile) # outfile
                );
                `$cmd`;
            } elseif ($this->isMovie()){
                $cmd = sprintf('convert %s[0] -resize %s^ -auto-orient -gravity center -extent %s  %s',
                    escapeshellarg($inFile), # infile
                    $size, # resize
                    $size, # extend
                    escapeshellarg($outFile) # outfile
                );
                `$cmd`;

            }

            if (file_exists($outFile)) {
                touch($outFile, filemtime($inFile));
            }
        }
    }

    public function isMovie()
    {
        return $this->masterFile->isMovie();
    }

    public function isImage()
    {
        return $this->masterFile->isImage();
    }

}
