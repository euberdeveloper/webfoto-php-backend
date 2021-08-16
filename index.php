<?php

define('WEBFOTO_SCRIPT', 'API');

require_once __DIR__ . '/src/bootstrap.php';

use Webfoto\Utils\DatabaseService;

$db = new DatabaseService();
$db->getImages('cortevalier');
