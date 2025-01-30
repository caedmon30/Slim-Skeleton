<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'twig_cache'     => false, // Should be commented in production
                //'twig_cache'  => __DIR__ . '/../tmp/cache',// Should be uncommented in production
                'logger' => [
                    'name' => 'slim-app',
                    'path' => __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => [
                    'driver' => 'mysql',
                    'host' => '192.168.0.102',
                    'database' => 'keys_db',
                    'username' => 'adminer',
                    'password' => 'Mehcserv1ce@',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'options' => [
                        // Turn off persistent connections
                        PDO::ATTR_PERSISTENT => false,
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Emulate prepared statements
                        PDO::ATTR_EMULATE_PREPARES => true,
                        // Set default fetch mode to array
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Set character set
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
                    ],
                ],

                'session' => [
                    'name' => 'keys_app',
                    'lifetime' => 7200,
                    'save_path' => null,
                    'domain' => null,
                    'secure' => false,
                    'httponly' => true,
                    'cache_limiter' => 'nocache',
                ],

                'states' => ['Draft', 'Submitted', 'Approved', 'Rejected', 'Ordered', 'Completed'],
                'transitions' => [
                    'Draft' => [
                        'submit' => 'Submitted',
                    ],
                    'Submitted' => [
                        'approve' => 'Approved',
                        'reject' => 'Rejected',
                    ],
                    'Approved' => [
                        'order' => 'Ordered',
                    ],
                    'Rejected' => [],
                    'Ordered' => [
                        'complete' => 'Completed',
                    ],
                ],
            ]);
        }
    ]);
};
