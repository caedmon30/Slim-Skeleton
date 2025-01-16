<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    // Add Twig-View Middleware
    global $container;
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->add(SessionMiddleware::class);
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(true, true, true);
};
