<?php

define('WEBFOTO_SCRIPT', 'API');

require_once __DIR__ . '/src/bootstrap.php';

use Webfoto\Utils\DatabaseService;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER["REQUEST_METHOD"];

$regexGetImages = '/^\/api\/albums\/(?P<name>\w+)\/images$/';
$regexGetLatest = '/^\/api\/albums\/(?P<name>\w+)\/images\/latest$/';

if ($requestMethod === 'GET' && preg_match($regexGetImages, $uri, $matches)) {
    $db = new DatabaseService();
    $name = $matches['name'];
    $images = array_map(fn($el) => $el->format(DateTime::ATOM), $db->getImages($name));
    $response = json_encode($images);
    echo $response;
} elseif ($requestMethod === 'GET' && preg_match($regexGetLatest, $uri, $matches)) {
    echo "get latest" . PHP_EOL;
} else {
    header("HTTP/1.1 404 Not Found");
}
