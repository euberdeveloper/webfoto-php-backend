<?php

define('WEBFOTO_CWD', getcwd());

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/autoload.php';

use Webfoto\Types\Image;
use Webmozart\PathUtil\Path;
use Webfoto\Utils\Drivers\DahuaDriver;
use Webfoto\Utils\DatabaseService;

$db = new DatabaseService();

$db->insertImage(new Image('xxx', new DateTime()));

$inputDir = Path::join(WEBFOTO_CWD, 'inputs', 'input', 'cortevalier');
$images = DahuaDriver::analyzeAlbum($inputDir);

foreach ($images as $image) {
    echo $image->timestamp->format(DateTime::ATOM) . "<br>";
}


/**
 * 1. Read config file (env)
 * 2. Read json file (albums)
 * 3. (For an album) Scan input folder and get input images
 * 4. Get from the database the last timestamp
 * 5. Smist to remove and to move
 * 6. Apply changes, by updating also the database
 */
