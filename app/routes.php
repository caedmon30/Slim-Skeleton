<?php

declare(strict_types=1);

use App\Application\Actions\Employee\CreateEmployeeAction;
use App\Application\Actions\Employee\DeleteEmployeeAction;
use App\Application\Actions\Employee\ListEmployeesAction;
use App\Application\Actions\Employee\UpdateEmployeeAction;
use App\Application\Actions\Employee\ViewEmployeeAction;
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
        return $view->render($response, 'pages/home.html.twig', []);
    })->setName('home');

    // web routes
    $app->get('/dashboard', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/dashboard.html.twig', []);
    })->setName('dashboard');

    $app->get('/admin', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/admin.html.twig', []);
    })->setName('admin');

    // api routes
    $app->group('/api/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->post('', CreateUserAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->delete('/{id}', DeleteUserAction::class);
        $group->put('/{id}', UpdateUserAction::class);
    });

    $app->group('/api/status', function (Group $group) {
        $group->get('', ListEmployeesAction::class);
        $group->post('', CreateEmployeeAction::class);
        $group->get('/{id}', ViewEmployeeAction::class);
        $group->delete('/{id}', DeleteEmployeeAction::class);
        $group->put('/{id}', UpdateEmployeeAction::class);
    });
};
