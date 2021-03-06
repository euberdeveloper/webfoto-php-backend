<?php

namespace Webfoto\Utils;

use DateTime;
use Exception;
use FaaPz\PDO\Database;
use FaaPz\PDO\Clause;

use Webfoto\Types\Image;

class DatabaseService
{

    private $pdo;

    private function createDatabaseIfNotExists(): void
    {
        $dbName = WEBFOTO_CONFIG['DB']['DATABASE'];
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbName};");
    }

    private function createTableIfNotExists(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS images (
                id INTEGER NOT NULL AUTO_INCREMENT, 
                name VARCHAR(255) NOT NULL, 
                path VARCHAR(1000) NOT NULL,
                timestamp DATETIME NOT NULL,
                PRIMARY KEY (id)
            );
        ");

        // TODO: add index by solving IF NOT EXISTS
        /*
            ALTER TABLE images
            ADD INDEX images_name_index(name)
            USING HASH;
        */
    }

    private function parseTimestamp(string $timestamp): DateTime {
        return new DateTime("{$timestamp} UTC");
    }


    function __construct()
    {
        $dbConfig = WEBFOTO_CONFIG['DB'];

        $dsn = "mysql:host={$dbConfig['HOST']};dbname={$dbConfig['DATABASE']};charset={$dbConfig['CHARSET']}";
        $usr = $dbConfig['USERNAME'];
        $pwd = $dbConfig['PASSWORD'];

        try {
            $this->pdo = new Database($dsn, $usr, $pwd);
        } catch (Exception $error) {
            $tempdsn = "mysql:host={$dbConfig['HOST']};charset={$dbConfig['CHARSET']}";
            $this->pdo = new Database($tempdsn, $usr, $pwd);
            $this->createDatabaseIfNotExists();
            $this->pdo = new Database($dsn, $usr, $pwd);
        }

        $this->createTableIfNotExists();
    }

    public function getLastImageDate($name): ?DateTime
    {
        $selectStmt = $this->pdo
            ->select(['timestamp'])
            ->from("images")
            ->orderBy('timestamp DESC')
            ->where(new Clause\Conditional("name", "=", $name))
            ->limit(new Clause\Limit(1));

        $stmt = $selectStmt->execute();
        $data = $stmt->fetch();

        return $data ? $this->parseTimestamp($data['timestamp']) : null;
    }

    public function getLastImagePath($name): ?string
    {
        $selectStmt = $this->pdo
            ->select(['path'])
            ->from("images")
            ->orderBy('timestamp DESC')
            ->where(new Clause\Conditional("name", "=", $name))
            ->limit(new Clause\Limit(1));

        $stmt = $selectStmt->execute();
        $data = $stmt->fetch();

        return $data ? $data['path'] : null;
    }

    public function insertImage(Image $image): void
    {
        $insertStmt = $this->pdo
            ->insert([
                'name' => $image->name,
                'path' => $image->path,
                'timestamp' => $image->timestamp->format('Y-m-d H:i:s')
            ])
            ->into("images");

        $insertStmt->execute();
    }

    public function getImages(string $name): array
    {
        $selectStmt = $this->pdo
            ->select(['timestamp'])
            ->from("images")
            ->orderBy('timestamp ASC')
            ->where(new Clause\Conditional("name", "=", $name));

        $stmt = $selectStmt->execute();

        $result = [];
        for($data = $stmt->fetch(); $data !== false; $data = $stmt->fetch()) {
            $date = $this->parseTimestamp($data['timestamp']);
            array_push($result, $date);
        }

        return $result;
    }
}
