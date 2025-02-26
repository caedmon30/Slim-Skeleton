<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Controllers\EmailController;
use App\Middleware\CasAuthenticationMiddleware;
use App\Middleware\LdapAuthorizationMiddleware;
use App\Middleware\SessionMiddleware;
use App\Services\CasService;
use App\Services\LdapService;
use App\Services\MailerService;
use App\Services\WorkflowService;
use buzzingpixel\twigswitch\SwitchTwigExtension;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPMailer\PHPMailer\PHPMailer;
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
            $twig = Twig::create(__DIR__ . '/../templates', ['cache' => $settings->get('twig_cache')]);
            // Ensure session is started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Add session as a global variable
            $twig->getEnvironment()->addGlobal('session', $_SESSION);
            $twig->addExtension(new SwitchTwigExtension());
            return $twig;
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

        PHPMailer::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $mailSettings = $settings->get('email');
            $mail = new PHPMailer(true);

            // Configure PHPMailer
            $mail->isSMTP();
            $mail->Host = $mailSettings['host']; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = $mailSettings['username']; // SMTP username
            $mail->Password = $mailSettings['password']; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
            $mail->Port = $mailSettings['port']; // SMTP port

            // Default sender details
            $mail->setFrom($mailSettings['address'], $mailSettings['name']);

            return $mail;
        },

        MailerService::class => function (ContainerInterface $c) {
            return new MailerService($c->get(PHPMailer::class));
        },
        EmailController::class => function (ContainerInterface $c) {
            return new EmailController($c->get(MailerService::class));
        },

        CasService::class => function (ContainerInterface $c) {
            return new CasService($c->get(SettingsInterface::class)->get('cas'));
        },
        LdapService::class => function (ContainerInterface $c) {
            return new LdapService($c->get(SettingsInterface::class)->get('ldap'));
        },
        CASAuthenticationMiddleware::class => function (ContainerInterface $c) {
            return new CASAuthenticationMiddleware($c->get(CasService::class));
        },
        LdapAuthorizationMiddleware::class => function (ContainerInterface $c) {
            return new LdapAuthorizationMiddleware($c->get(LdapService::class));
        },
        SessionMiddleware::class => function (ContainerInterface $c) {
            return new SessionMiddleware(
                $c->get(SettingsInterface::class),
                $c->get(CasService::class),
                $c->get(LdapService::class),
            );
        },

    ]);
};
