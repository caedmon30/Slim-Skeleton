<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Nette\Database\Connection;

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
            // remove comment in production
            //return Twig::create(__DIR__ . '/../templates', ['cache' => __DIR__ . '/../tmp/cache']);
            // comment out in production
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        },
        // Database connection
        Connection::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $mdbSettings = $settings->get('db');
            $dsn = 'mysql:host=' . $mdbSettings['host'] . '; dbname=' . $mdbSettings['database'];
            $user = $mdbSettings['username'];
            $pass = $mdbSettings['password'];
            return new Connection($dsn, $user, $pass);
        },

        // PHP Sessions
        SessionManagerInterface::class => function (ContainerInterface $container) {
            return $container->get(SessionInterface::class);
        },

        SessionInterface::class => function (ContainerInterface $container) {
            $options = $container->get('settings')['session'];

            return new PhpSession($options);
        },
    ]);
};
