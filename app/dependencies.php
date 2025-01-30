<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Services\WorkflowService;
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
        // Logger Configuration
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $loggerSettings = $settings->get('logger');

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());

            // Ensure level is a Monolog constant
            $logLevel = $loggerSettings['level'] ?? Logger::DEBUG;
            $logger->pushHandler(new StreamHandler($loggerSettings['path'], $logLevel));

            return $logger;
        },

        // Twig Templating Engine
        Twig::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return Twig::create(__DIR__ . '/../templates', ['cache' => $settings->get('twig_cache')]);
        },

        // Database Connection (Using Charset)
        Connection::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $dbSettings['host'],
                $dbSettings['database']
            );
            return new Connection($dsn, $dbSettings['username'], $dbSettings['password']);
        },

        // Workflow Service
        WorkflowService::class => function (ContainerInterface $c) {
            return new WorkflowService($c->get(Connection::class));
        },

        // PHP Session Manager
        SessionManagerInterface::class => fn(ContainerInterface $c) => $c->get(SessionInterface::class),

        // PHP Session
        SessionInterface::class => function (ContainerInterface $c) {
            $options = $c->get(SettingsInterface::class)->get('session');
            return new PhpSession($options);
        },
    ]);
};
