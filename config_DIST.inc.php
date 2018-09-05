<?php


$hostname = `hostname`;
if (strpos($hostname, 'myC64atHome') !== false) {
    $this->filePathToThings = __DIR__ . '/../my_photos';
} elseif (strpos($hostname, 'myWebServer') !== false) {
    $this->filePathToThings = '/home/me/MyPhotos';
} else {
    die("Your config here:\n" . __FILE__ . ': Line ' . __LINE__);
}

$this->filePathToCache = __DIR__ . '/../Cache/Ereignisse2';
$this->filePathToRepo = __DIR__ . '/../Ereignisse2.repo';

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

$this->blacklistedFoldersRegExp='.*/(_misc|tmp)(/.*|$)';

$this->cmdFind= preg_match('/darwin/uis',`uname -a`)? 'gfind' : 'find';

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
