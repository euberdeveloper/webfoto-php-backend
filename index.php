<?php

define('WEBFOTO_SCRIPT', 'API');

require_once __DIR__ . '/src/bootstrap.php';

use Webfoto\Utils\DatabaseService;

function addHeaders(): void
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Content-Type: application/json");
}

function getProtocol(): string
{
    return (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http';
}

function getHost(): string
{
    return $_SERVER['HTTP_HOST'];
}

function getUri(): string
{
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

function getServerMethod(): string
{
    return $_SERVER['REQUEST_METHOD'];
}

function main(): void
{
    addHeaders();

    $uri = getUri();
    $requestMethod = getServerMethod();

    $regexGetImages = '/^\/api\/albums\/(?P<name>\w+)\/images$/';
    $regexGetLatest = '/^\/api\/albums\/(?P<name>\w+)\/images\/latest$/';

    if ($requestMethod === 'GET' && preg_match($regexGetImages, $uri, $matches)) {
        $db = new DatabaseService();
        $name = $matches['name'];
        $images = array_map(fn ($el) => $el->format(DateTime::ATOM), $db->getImages($name));
        $response = json_encode($images);
        echo $response;
    } elseif ($requestMethod === 'GET' && preg_match($regexGetLatest, $uri, $matches)) {
        $protocol = getProtocol();
        $host = getHost();
        $name = $matches['name'];
        $db = new DatabaseService();
        $path = $db->getLastImagePath($name);
        $response = json_encode("{$protocol}://{$host}{$path}");
        echo $response;
    } else {
        header("HTTP/1.1 404 Not Found");
    }
}
main();
