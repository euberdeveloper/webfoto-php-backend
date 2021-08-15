<?php

namespace Webfoto\Utils;

class DatabaseService
{

    private $pdo;

    function __construct()
    {
        $dbConfig = WEBFOTO_CONFIG['DB'];

        $dsn = "mysql:host={$dbConfig['HOST']};dbname={$dbConfig['DATABASE']};charset={$dbConfig['CHARSET']}";
        $usr = $dbConfig['USERNAME'];
        $pwd = $dbConfig['PASSWORD'];

        echo $dsn;
    }
}
