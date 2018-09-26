<?php
/**
 * Class File
 *
 */

namespace App\Entity;

use App\Manager\ThingsManager;

class FileEntity
{
    public $thingsManager;
    public $relFileName;
    public $absFileName;
    public $absDirName;
    public $baseDir;


    public $relDirName;
    public $basename;
    public $extension;
    public $filename;

    public function dump()
    {

        echo "relFileName: " . $this->relFileName . "\n";
        echo "absFileName: " . $this->absFileName . "\n";
        echo "baseDir: " . $this->baseDir . "\n";
        echo "relDirName: " . $this->relDirName . "\n";
        echo "basename: " . $this->basename . "\n";
        echo "extension: " . $this->extension . "\n";
        echo "filename: " . $this->filename . "\n";
    }

    public function __construct(ThingsManager $thingsManager, $baseDir, $relFileName)
    {
        $this->thingsManager = $thingsManager;

        $this->baseDir = $baseDir;
        $this->relFileName = $relFileName;

        $this->absFileName = $this->baseDir . "/" . $this->relFileName;

        $path_parts = pathinfo($this->absFileName);

        $this->relDirName = pathinfo($relFileName, PATHINFO_DIRNAME);
        if ($this->relDirName === '.') $this->relDirName = '';

        $this->absDirName = $path_parts['dirname'];
        $this->basename = $path_parts['basename'];
        $this->extension = $path_parts['extension'];
        $this->filename = $path_parts['filename'];


    }

    public function __toString()
    {
        return $this->absFileName;
    }

    public function isProprietaryMovie()
    {
        return isset($this->extension) && strlen($this->extension) && false !== array_search(strtolower($this->extension), $this->thingsManager->getConfigHelper()->fileExtensionsOfProprietaryMovies);
    }

    public function isMovie()
    {
        return isset($this->extension) && strlen($this->extension) && false !== array_search(strtolower($this->extension), $this->thingsManager->getConfigHelper()->fileExtensionsOfMovies);
    }

    public function isImage()
    {
        return isset($this->extension) && strlen($this->extension) && false !== array_search(strtolower($this->extension), $this->thingsManager->getConfigHelper()->fileExtensionsOfImages);
    }
}
