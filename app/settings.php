<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Spiral\Database\Driver\MySQL\MySQLDriver;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => DEBUG, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : ROOT_DIR . '/logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'connections' => [
                'mysql' => [
                    'driver' => MySQLDriver::class,
                    'options' => [
                        'connection' => 'mysql:host=127.0.0.1;dbname=oauth2server',
                        'username' => 'root',
                        'password' => $_ENV['MY_SQL_DB_PASSWORD'],
                        // 'timezone' => 'Asia/Kolkata'
                    ]
                ],
            ],
            'privateKeyPath' => ROOT_DIR . '/private.key',
            'publicKeyPath' => ROOT_DIR . '/public.key',
        ],
    ]);
};
