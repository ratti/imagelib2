<?php

namespace App\Entity;

use App\Manager\ThingsManager;

class FolderEntity
{
    public $thingsManager;

    public $relPath;
    public $baseDir;
    public $title;
    public $id;

    public function __construct(ThingsManager $thingsManager, $baseDir, $relPath)
    {
        $this->thingsManager = $thingsManager;

        $this->baseDir = $baseDir;
        $this->relPath = $relPath;
        $this->title = preg_replace('/^.*\//uis', '', $relPath);
    }

    public function dump(){
        echo "id: ".$this->id."\n";
        echo "baseDir: ".$this->baseDir."\n";
        echo "relPath: ".$this->relPath."\n";
        echo "title: ".$this->title."\n";
    }

}
