<?php

require __DIR__ . '/vendor/autoload.php';

require_once 'src/types/DriverType.php';
require_once 'src/types/InputImage.php';
require_once 'src/utils/drivers/BaseDriver.php';
require_once 'src/utils/drivers/DahuaDriver.php';

use Webmozart\PathUtil\Path;
use Webfoto\Utils\Drivers\DahuaDriver;

$inputDir = Path::join(getcwd(), 'inputs', 'input');
$images = DahuaDriver::analyzeAlbum($inputDir);

$a = new DateTime();

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