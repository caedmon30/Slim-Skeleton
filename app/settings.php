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

                'cas'=> [
                    'cas_host' => '192.168.0.102',
                    'cas_port' => 443,
                    'cas_context' => '/cas',
                    'service_base_url' => 'http://localhost:8000',
                    'cas_server_ca_cert_path' => __DIR__ . '/../certs/USERTrust_RSA_Certification_Authority.pem',
                ],
                'ldap'=>[
                    'host' => 'ldap.forumsys.com', // Replace with your LDAP server hostname/IP
                    'port' => 389,                // Typically 389 (LDAP) or 636 (LDAPS)
                    'base_dn' => 'cn=read-only-admin,dc=example,dc=com', // Replace with your base DN
                    'username_attribute' => 'uid',   // Attribute to use for username (e.g., uid, samaccountname)
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

                'email' => [
                    'host' => 'sandbox.smtp.mailtrap.io',
                    'username' => '304e0a5ab5357b',
                    'password' => '00b077a0530a0f',
                    'port' => 2525,
                    'address' => 'chem-keykeeper@umd.edu',
                    'name' => 'Chemistry KeyManager',
                ],

                'states' => ['Draft', 'Submitted', 'Approved', 'Rejected', 'Ordered', 'Completed'],
                'states_colors' => ['#FF0000', '#FF7F00', '#FFFF00', '#00FF00', '#0000FF', '#8B008B'],
            ]);
        }
    ]);
};
