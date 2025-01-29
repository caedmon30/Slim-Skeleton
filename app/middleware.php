<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {

    global $container;
    $app->add(SessionMiddleware::class);
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(true, true, true);
};
