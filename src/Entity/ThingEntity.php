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

class ThingEntity extends AbstractEntity
{
    /* @var \App\Entity\FileEntity $masterFile */
    public $masterFile;

    /* @var \App\Entity\FileEntity $thumbnailFile */
    public $thumbnailFile;

    /* @var \App\Entity\FileEntity $posterFile */
    public $posterFile;

    /* @var \App\Entity\FileEntity $derivedVideoFile */
    public $derivedVideoFile;

    /* @var \App\Entity\FileEntity $exifFile */
    public $exifFile;

    public $masterExif;

    public $folderId;

    public $conversionCommand = array(
        'IMAGE_TO_EXIF' => 'exiftool -f -n -j -b {inFile} > {outFile}',
        'IMAGE_TO_THUMBNAIL' => 'convert {inFile} -resize {THUMBNAILSIZE} -auto-orient -gravity center -extent {THUMBNAILSIZE}  {outFile}',
        'IMAGE_TO_POSTER' => 'convert {inFile} -resize {POSTERSIZE} -auto-orient -gravity center -extent {POSTERSIZE}  {outFile}',
        'PROPRIETARY_MOVIE_TO_MP4' => 'ffmpeg -y -i {inFile} -c:v libx264 -c:a aac -pix_fmt yuv420p -movflags faststart -hide_banner {outFile}',
        'MOVIE_TO_THUMBNAIL' => 'convert {inFile}[0] -resize {THUMBNAILSIZE} -auto-orient -gravity center -extent {THUMBNAILSIZE} {outFile}',
    );

    public function dump()
    {

        echo "id: " . $this->id . "\n";

        echo "folder id: " . $this->folderId . "\n";

        echo "master file:\n";
        $this->masterFile->dump();
        echo "thumbnail file:\n";
        $this->thumbnailFile->dump();
        echo "poster file:\n";
        $this->posterFile->dump();
        echo "convertedVideo file:\n";
        $this->derivedVideoFile->dump();
        echo "exif file:\n";
        $this->exifFile->dump();
    }

    public function __construct(ThingsManager $thingsManager, FileEntity $file)
    {
        $this->thingsManager = $thingsManager;

        $this->masterFile = $file;
        $this->thumbnailFile = $this->generatePath('thumbnail');
        $this->posterFile = $this->generatePath('poster');
        $this->derivedVideoFile = $this->generatePath('video');
        $this->exifFile = $this->generatePath('exif');

        #$this->setExif();
    }

    public function generatePath($prefix)
    {
        $baseDir = $this->thingsManager->getConfigHelper()->filePathToCache . '/' . $prefix;
        $ext = 'jpg';
        if ($prefix == 'exif') $ext = 'json';
        if ($prefix == 'video') $ext = 'mp4';
        return new FileEntity($this->thingsManager, $baseDir, $this->masterFile->relFileName . ".{$ext}");
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
        if ($this->isProprietaryMovie()) {
            $this->createDerivedVideo();
            $this->createDerivedPosterFile();
            $this->createDerivedThumbnailFileFromDerivedVideo();
        } elseif ($this->isMovie()) {
            $this->createDerivedThumbnailFile();
            $this->createDerivedPosterFile();
        } elseif ($this->isImage()) {
            $this->createDerivedThumbnailFile();
            $this->createDerivedPosterFile();
            $this->createDerivedExifFile();
        }
    }

    public function createDerivedVideo()
    {
        $inFile = $this->masterFile->absFileName;
        $outFile = $this->derivedVideoFile->absFileName;
        $this->createDerivedFileAbstract($inFile, $outFile, 'nonsense.');
    }

    public function createDerivedThumbnailFile()
    {
        $inFile = $this->masterFile->absFileName;
        $outFile = $this->thumbnailFile->absFileName;
        $this->createDerivedFileAbstract($inFile, $outFile, '256x256');
    }

    public function createDerivedThumbnailFileFromDerivedVideo()
    {
        $inFile = $this->derivedVideoFile->absFileName;
        $outFile = $this->thumbnailFile->absFileName;
        $this->createDerivedFileAbstract($inFile, $outFile, '256x256');
    }


    public function createDerivedPosterFile()
    {
        $inFile = $this->masterFile->absFileName;
        $outFile = $this->posterFile->absFileName;
        $this->createDerivedFileAbstract($inFile, $outFile, '1248x960');
    }

    public function createDerivedExifFile()
    {
        $inFile = $this->masterFile->absFileName;
        $outFile = $this->exifFile->absFileName;
        $this->createDerivedFileAbstract($inFile, $outFile, 'nonsense.');
    }

    public function createDerivedFileFromCmd($cmd, $inFile, $outFile)
    {

        $outDir = dirname($outFile);

        if (!is_dir($outDir)) mkdir($outDir, 0777, true);

        if (!file_exists($outFile) || filemtime($inFile) !== filemtime($outFile)) {

            $cmd = str_replace('{inFile}', escapeshellarg($inFile), $cmd);
            $cmd = str_replace('{outFile}', escapeshellarg($outFile), $cmd);
            $cmd = str_replace('{THUMBNAILSIZE}', '256x256', $cmd);
            $cmd = str_replace('{POSTERSIZE}', '1248x960', $cmd);

            echo "$cmd\n";
            if (file_exists($outFile)) {
                touch($outFile, filemtime($inFile));
            }
        }

        if (preg_match('/\.json/uis', $outFile) && is_null($this->masterExif) && file_exists($outFile)) {
            # load exif if not generated this time, but json file exists
            $this->loadExif();
        }
    }


    public function createDerivedFileAbstract($inFile, $outFile, $size = '')
    {
        $outDir = dirname($outFile);

        if (!is_dir($outDir)) mkdir($outDir, 0777, true);

        if (!file_exists($outFile) || filemtime($inFile) !== filemtime($outFile)) {

            if (preg_match('/\.json/uis', $outFile)) {
                $cmd = 'exiftool -f -n -j -b ' . escapeshellarg($inFile);
                $json = `$cmd`;
                $obj = json_decode($json)[0];

                unset($obj->ThumbnailImage);
                unset($obj->OtherImage);

                $this->masterExif = $obj;
                file_put_contents($outFile, $json);

            } elseif ($this->isImage()) {
                $cmd = sprintf('convert %s -resize %s -auto-orient -gravity center -extent %s  %s',
                    escapeshellarg($inFile), # infile
                    $size, # resize
                    $size, # extend
                    escapeshellarg($outFile) # outfile
                );
                `$cmd`;
            } elseif ($this->isProprietaryMovie()) {
                $cmd = sprintf('ffmpeg -y -i %s -c:v libx264 -c:a aac -pix_fmt yuv420p -movflags faststart -hide_banner %s',
                    escapeshellarg($inFile), # infile
                    escapeshellarg($outFile) # outfile
                );
                echo "\n\n\n\n\n\n\n\n\n$cmd\n";
                `$cmd`;
            } elseif ($this->isMovie()) {
                $cmd = sprintf('convert %s[0] -resize %s -auto-orient -gravity center -extent %s  %s',
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

        if (preg_match('/\.json/uis', $outFile) && is_null($this->masterExif) && file_exists($outFile)) {
            # load exif if not generated this time, but json file exists
            $this->loadExif();
        }
    }

    public function loadExif()
    {
        if (file_exists($this->exifFile->absFileName)) {
            $json = file_get_contents($this->exifFile->absFileName);
            $this->masterExif = json_decode($json);
        }
    }

    public function isProprietaryMovie()
    {
        return $this->masterFile->isProprietaryMovie();
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
