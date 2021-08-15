<?php

namespace Webfoto\Utils;

use DateTime;
use Exception;

use Webmozart\PathUtil\Path;

use Webfoto\Types\DriverType;
use Webfoto\Types\Image;

use Webfoto\Utils\DatabaseService;
use Webfoto\Utils\Drivers\BaseDriver;
use Webfoto\Utils\Drivers\DahuaDriver;

class ImagesHandler
{
    private string $name;
    private string $inputPath;
    private string $outputPath;
    private DriverType $driverType;
    private int $keepEverySeconds;
    private DatabaseService $db;

    private BaseDriver $driver;

    private function getDriver(): void
    {
        switch ($this->driverType) {
            case DriverType::DAHUA():
                $this->driver = new DahuaDriver();
                break;
            default:
                throw new Exception("Driver {$this->driverType} not found");
        }
    }

    private function getNextMinimumTimetamp(?DateTime $timestamp): ?DateTime
    {
        $secs = $timestamp === null ? null : strtotime($timestamp->format('Y-m-d H:i:s \U\T\C')) + $this->keepEverySeconds;
        return $secs === null ? null : new Datetime("@{$secs}");
    }

    function __construct($settings)
    {
        $this->name = $settings['name'];
        $this->inputPath = Path::join(WEBFOTO_CWD, $settings['inputPath']);
        $this->driverType = new DriverType($settings['driver']);
        $this->keepEverySeconds = $settings['keepEverySeconds'];

        $this->outputPath = Path::join(WEBFOTO_CONFIG['OUTPUT_FOTOS_PATH'], $this->name);
        if (!file_exists($this->outputPath)) {
            mkdir($this->outputPath, 0777, true);
        }

        $this->getDriver();

        $this->db = new DatabaseService();
    }

    public function handle(): void
    {
        $inputImages = array_slice(DahuaDriver::analyzeAlbum($this->inputPath), 0, 10);
        $lastTimestamp = $this->db->getLastImageDate();

        $toDeleteImages = [];
        $toSaveImages = [];

        $currentTimestamp = $this->getNextMinimumTimetamp($lastTimestamp);
        foreach ($inputImages as $image) {
            echo PHP_EOL . PHP_EOL;
            if ($currentTimestamp === null || $image->timestamp >= $currentTimestamp) {
                array_push($toSaveImages, $image);
                $currentTimestamp = $this->getNextMinimumTimetamp($image->timestamp);
            } else {
                array_push($toDeleteImages, $image);
            }
        }

        foreach ($toDeleteImages as $image) {
            unlink($image->path);
        }
        foreach ($toSaveImages as $image) {
            $filename = $image->timestamp->format('Y-m-d\TH:i:s') . '.jpg';
            $imagePath = "/{$this->name}/{$filename}";
            $toSaveImage = new Image(
                $this->name,
                $imagePath,
                $image->timestamp
            );
            $this->db->insertImage($toSaveImage);
            rename($image->path, Path::join($this->outputPath, $filename));
        }
    }
}
