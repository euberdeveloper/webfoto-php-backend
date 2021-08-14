<?php

namespace Webfoto\Utils\Drivers;

use DateTime;
use Webfoto\Types\InputImage;
use Webfoto\Utils\Drivers\BaseDriver;

abstract class DahuaDriver extends BaseDriver
{
    protected static function extractDate(string $filename): DateTime
    {
        $datePart = explode('[', $filename)[0];

        $year = substr($datePart, 0, 4);
        $month = substr($datePart, 4, 2);
        $date = substr($datePart, 6, 2);
        $hours = substr($datePart, 8, 2);
        $minutes = substr($datePart, 10, 2);
        $seconds = substr($datePart, 12, 2);

        $timestamp = strtotime("{$year}-{$month}-{$date} {$hours}:{$minutes}:{$seconds} UTC");
        return new DateTime("@{$timestamp}");
    }

    public static function analyzeAlbum(string $albumPath): array
    {
        return parent::analyzeAlbumHelper($albumPath, fn ($filename) => self::extractDate($filename));
    }
}
