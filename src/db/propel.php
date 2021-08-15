<?php

define('WEBFOTO_CWD', getcwd());
require_once getcwd() . '/src/utils/Config.php';

$dbHost = WEBFOTO_CONFIG['DB']['HOST'];
$dbName = WEBFOTO_CONFIG['DB']['DATABASE'];
$dbCharset = WEBFOTO_CONFIG['DB']['CHARSET'];
$dbUser = WEBFOTO_CONFIG['DB']['USERNAME'];
$dbPass = WEBFOTO_CONFIG['DB']['PASSWORD'];

return [
    'propel' => [
        'database' => [
            'connections' => [

                'webfoto' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}",
                    'user'       => $dbUser,
                    'password'   => $dbPass,
                    'attributes' => []
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'webfoto',
            'connections' => ['webfoto']
        ],
        'generator' => [
            'defaultConnection' => 'webfoto',
            'connections' => ['webfoto']
        ]
    ]
];
