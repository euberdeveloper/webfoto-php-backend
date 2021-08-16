<?php

define('WEBFOTO_SCRIPT', 'CRONJOB');

require_once __DIR__ . '/src/bootstrap.php';

use Webfoto\Utils\ImagesHandler;

echo "Start job" . PHP_EOL;

foreach (WEBFOTO_SETTINGS as $settings) {
    echo "Handling {$settings['name']}" . PHP_EOL;
    $handler = new ImagesHandler($settings);
    $handler->handle();
}

echo "Finished cronjob" . PHP_EOL;
