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
                path VARCHAR(1000) NOT NULL,
                timestamp DATETIME NOT NULL,
                PRIMARY KEY (id)
            );
        ");
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

    public function getLastImageDate()
    {
        $selectStmt = $this->pdo
            ->select(['timestamp'])
            ->from("images")
            ->orderBy('timestamp DESC')
            ->limit(new Clause\Limit(1));

        $stmt = $selectStmt->execute();
        $data = $stmt->fetch();

        $timestamp = $data ? strtotime($data['timestamp']) : null;
        return $timestamp ? new DateTime("@{$timestamp}") : null;
    }

    public function insertImage(Image $image): void
    {
        sleep(1);
        $insertStmt = $this->pdo
            ->insert([
                'path' => $image->path,
                'timestamp' => $image->timestamp->format('Y-m-d H:i:s')
            ])
            ->into("images");

        $insertStmt->execute();
    }

}
