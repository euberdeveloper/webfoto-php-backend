<?php

namespace Webfoto\Utils;

use Exception;

use Webfoto\Types\DriverType;
use Webfoto\Utils\Drivers\BaseDriver;
use Webfoto\Utils\Drivers\DahuaDriver;

class ImagesHandler
{
    private string $name;
    private string $inputPath;
    private string $outputPath;
    private DriverType $driverType;
    private int $keepEverySeconds;

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

    function __construct($settings)
    {
        $this->name = $settings['name'];
        $this->inputPath = $settings['inputPath'];
        $this->driverType = new DriverType($settings['driver']);
        $this->keepEverySeconds = $settings['keepEverySeconds'];

        $this->outputPath = WEBFOTO_CONFIG['OUTPUT_FOTOS_PATH'];
        $this->getDriver();
    }

    public function handle(): void
    {
    }
}
