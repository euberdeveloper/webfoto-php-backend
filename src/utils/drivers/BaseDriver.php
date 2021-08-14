<?php

namespace Webfoto\Utils\Drivers;

use DateTime;
use wherw\ScanPath;
use Webmozart\PathUtil\Path;

use Webfoto\Types\InputImage;

abstract class BaseDriver
{

    protected static function extractImagesFromFolder(string $path): array
    {
        $scan = new ScanPath();
        $scan->setPath($path);
        $scan->setExtension(['jpg']);

        return $scan->getFiles()->toArray();
    }

    protected static function getFileName(string $path): string
    {
        return Path::getFileName($path);
    }

    protected abstract static function extractDate(string $filename): DateTime;

    protected static function parseImage(string $path, callable $extractDate): InputImage
    {
        $filename = self::getFileName($path);
        $timestamp = $extractDate($filename);
        return new InputImage($path, $timestamp);
    }

    protected static function analyzeAlbumHelper(string $albumPath, callable $extractDate): array
    {
        $rawFiles = self::extractImagesFromFolder($albumPath);
        $result =  array_map(fn ($path) => self::parseImage($path, $extractDate), $rawFiles);
        usort($result, fn ($x, $y) => $x->timestamp->getTimestamp() - $y->timestamp->getTimestamp());
        return $result;
    }
}
