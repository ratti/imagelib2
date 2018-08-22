<?php

namespace App\Entity;

use App\Manager\ThingsManager;

class FolderEntity extends AbstractEntity
{
    public $relPath;
    public $baseDir;
    public $title;
    public $thingsIds = array();
    public $subfolderIds = array();
    public $parentRelPath;
    public $parentId;


    public function __construct(ThingsManager $thingsManager, $baseDir, $relPath)
    {
        $this->thingsManager = $thingsManager;

        $this->baseDir = $baseDir;
        $this->relPath = $relPath;
        $this->title = preg_replace('/^.*\//uis', '', $relPath);

        preg_match('/(^.*)\//uis', $this->relPath, $tmp);
        if (array_key_exists(1, $tmp)) {
            $this->parentRelPath = $tmp[1];
        }
    }

    public function dump()
    {
        echo "id: " . $this->id . "\n";
        echo "baseDir: " . $this->baseDir . "\n";
        echo "relPath: " . $this->relPath . "\n";
        echo "title: " . $this->title . "\n";
        echo "things: " . join(',', $this->thingsIds) . "\n";
        echo "subfoldersIds: " . join(',', $this->subfolderIds) . "\n";
        echo "parentRelPath: " . $this->parentRelPath . "\n";
        echo "parentId: " . $this->parentId . "\n";
    }

}
