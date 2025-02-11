<?php

declare(strict_types=1);

use App\Middleware\CasAuthenticationMiddleware;
use App\Middleware\LdapAuthorizationMiddleware;
use App\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {

    global $container;
    // Add Session Middleware (MUST be first)
    $app->add(SessionMiddleware::class);

    // Add CAS Authentication Middleware (runs after SessionMiddleware)
    //$app->add(CasAuthenticationMiddleware::class);

    // Add LDAP Authorization Middleware (runs after Session and CAS Auth Middleware)
    //$app->add(LdapAuthorizationMiddleware::class);

    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(false, true, true);
};
