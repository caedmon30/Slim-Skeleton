<?php

declare(strict_types=1);

use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\DeleteUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\UpdateUserAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'home.html.twig', []);
    })->setName('home');

    // web routes
    $app->get('/dashboard', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'dashboard.html.twig', []);
    })->setName('dashboard');

    $app->get('/users', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'users.html.twig', []);
    })->setName('users');

    // api routes
    $app->group('/api/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->post('', CreateUserAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->delete('/{id}', DeleteUserAction::class);
        $group->put('/{id}', UpdateUserAction::class);
    });
};
