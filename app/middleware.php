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
    // 1. Session Middleware (Manages user sessions) (MUST be first)
    $app->add(SessionMiddleware::class);

    // 2. Error Handling Middleware (Handles uncaught exceptions)
    $app->addErrorMiddleware(true, true, true);

    // 3. Body Parsing Middleware (Parses JSON, form data, etc.)
    $app->addBodyParsingMiddleware();

    // 4. CAS Authentication Middleware (Handles CAS-based authentication)
    //$app->add(CasAuthenticationMiddleware::class);

    // 5. LDAP Authorization Middleware (Checks user roles/permissions) (runs after Session and CAS Auth Middleware)
    //$app->add(LdapAuthorizationMiddleware::class);

    // 6. Routing Middleware (Processes route matching)
    $app->addRoutingMiddleware();

    // 7. Twig Middleware (Handles rendering views)
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));


};
