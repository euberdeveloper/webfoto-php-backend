<?php

namespace Webfoto\Utils\Config;

use Dotenv\Dotenv;
use Webmozart\PathUtil\Path;

class Config
{
    public array $config;
    public $settings;

    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(WEBFOTO_CWD);
        $dotenv->safeLoad();

        $dotenv->required('DB_HOST');
        $dotenv->required('DB_PORT')->isInteger();
        $dotenv->required('DB_DATABASE');
        $dotenv->required('DB_USER');
        $dotenv->required('DB_PASSWORD');
    }

    private function createConfig(): void
    {
        $this->config = [
            'DB' => [
                'HOST' => $_ENV['DB_HOST'],
                'PORT' => $_ENV['DB_PORT'],
                'DATABASE' => $_ENV['DB_DATABASE'],
                'USERNAME' => $_ENV['DB_USER'],
                'PASSWORD' => $_ENV['DB_PASSWORD'],
                'CHARSET' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            ],
            'SETTINGS_PATH' => $_ENV['SETTINGS_PATH'] ?? './settings.json',
            'OUTPUT_FOTOS_PATH' => $_ENV['OUTPUT_FOTOS_PATH'] ?? './outputs'
        ];
    }

    private function createSettings(): void
    {
        $raw_json = file_get_contents(Path::join(WEBFOTO_CWD, $this->config['SETTINGS_PATH']));
        $this->settings = json_decode($raw_json, true);
    }

    function __construct()
    {
        $this->loadEnv();
        $this->createConfig();
        $this->createSettings();
    }
}

if (!isset($webfotoConfig)) {
    $webfotoConfig = new Config();
    define('WEBFOTO_CONFIG', $webfotoConfig->config);
    define('WEBFOTO_SETTINGS', $webfotoConfig->settings);
}
