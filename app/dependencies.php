<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Selective\Database\Connection;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        Twig::class => function (ContainerInterface $c) {
            //return Twig::create(__DIR__ . '/../templates', ['cache' => __DIR__ . '/../tmp/cache']); //PRODUCTION
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]); //DEVELOPMENT
        },
        // Database connection
        Connection::class => function (ContainerInterface $c) {
            return new Connection($c->get(PDO::class));
        },

        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');

            $driver = $dbSettings['driver'];
            $host = $dbSettings['host'];
            $dbname = $dbSettings['database'];
            $username = $dbSettings['username'];
            $password = $dbSettings['password'];
            $charset = $dbSettings['charset'];
            $flags = $dbSettings['flags'];
            $dsn = "$driver:host=$host;dbname=$dbname;charset=$charset";

            return new PDO($dsn, $username, $password, $flags);
        },
        MeekroDB::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $mdbSettings = $settings->get('db');
            try {
                $dsn = 'mysql:host=' . $mdbSettings['host'] . '; dbname=' . $mdbSettings['database'];
                $user = $mdbSettings['username'];
                $pass = $mdbSettings['password'];
                return new MeekroDB($dsn, $user, $pass);
            } catch (MeekroDBException $e) {
                echo "Connection error " . $e->getMessage();
                exit;
            }
        },
    ]);
};
