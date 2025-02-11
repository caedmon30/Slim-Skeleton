<?php

declare(strict_types=1);

use App\Controllers\Admin\LogController;
use App\Services\WorkflowService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $container = $app->getContainer(); // Get DI container

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response; // CORS Pre-Flight OPTIONS Request Handler
    });

    $app->get('/', function (Request $request, Response $response) use ($container) {
        $view = $container->get(Twig::class);
        return $view->render($response, 'pages/home.html.twig', []);
    })->setName('home');

    // Web routes
    $app->get('/dashboard', function (Request $request, Response $response) use ($container) {
        $view = $container->get(Twig::class);
        return $view->render($response, 'pages/dashboard.html.twig', []);
    })->setName('dashboard');

    $app->get('/keys', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/keys.html.twig', []);
    })->setName('keys');

    $app->get('/request-create', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'forms/request.html.twig', []);
    })->setName('request-create');

    $app->get('/requests', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/requests.html.twig', []);
    })->setName('requests');

    $app->get('/reports', function ($request, $response, $args) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/reports.html.twig', []);
    })->setName('reports');

    $app->group('/settings', function (Group $group) use ($container) {
        $view = $container->get(Twig::class);

        $group->get('', function (Request $request, Response $response) use ($view) {
            return $view->render($response, 'pages/admin.html.twig', []);
        })->setName('settings');

        $group->get('/users', function (Request $request, Response $response) use ($view) {
            return $view->render($response, 'pages/users.html.twig', []);
        })->setName('users');

        $group->get('/employee-status', function ($request, $response, $args) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'pages/employee-types.html.twig', []);
        })->setName('employee-status');
    });

    $app->group('/admin/logs', function (Group $group) {
        $group->get('', [LogController::class, 'index'])->setName('logs');
        $group->get('/export/csv', [LogController::class, 'exportCsv'])->setName('admin.logs.export.csv');
        $group->get('/export/pdf', [LogController::class, 'exportPdf'])->setName('admin.logs.export.pdf');
    });

    // Workflow Routing (Uses Dependency Injection)
    $app->group('/workflow', function (Group $group) use ($container) {
        $workflowService = $container->get(WorkflowService::class);

        $group->post(
            '/submit/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->submitRequest((int)$args['id']);
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/approve/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->approveRequest((int)$args['id']);
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/reject/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->rejectRequest((int)$args['id']);
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/order/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->orderRequest((int)$args['id']);
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/complete/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->completeRequest((int)$args['id']);
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );
    });
};

