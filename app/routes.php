<?php

declare(strict_types=1);

use App\Application\Actions\Employee\CreateEmployeeAction;
use App\Application\Actions\Employee\DeleteEmployeeAction;
use App\Application\Actions\Employee\ListEmployeesAction;
use App\Application\Actions\Employee\UpdateEmployeeAction;
use App\Application\Actions\Employee\ViewEmployeeAction;
use App\Application\Actions\Key\CreateKeyAction;
use App\Application\Actions\Key\DeleteKeyAction;
use App\Application\Actions\Key\ListKeysAction;
use App\Application\Actions\Key\UpdateKeyAction;
use App\Application\Actions\Key\ViewKeyAction;
use App\Application\Actions\Request\CreateRequestAction;
use App\Application\Actions\Request\DeleteRequestAction;
use App\Application\Actions\Request\ListRequestAction;
use App\Application\Actions\Request\UpdateRequestAction;
use App\Application\Actions\Request\ViewRequestAction;
use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\DeleteUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\UpdateUserAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\LdapDetailsMiddleware;
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


    // API Routes
    $app->group('/api/keys', function (Group $group) {
        $group->get('', ListKeysAction::class);
        $group->post('', CreateKeyAction::class);
        $group->get('/{id}', ViewKeyAction::class);
        $group->delete('/{id}', DeleteKeyAction::class);
        $group->put('/{id}', UpdateKeyAction::class);
    });

    $app->group('/api/requests', function (Group $group) {
        $group->get('', ListRequestAction::class);
        $group->post('', CreateRequestAction::class);
        $group->get('/{id}', ViewRequestAction::class);
        $group->delete('/{id}', DeleteRequestAction::class);
        $group->put('/{id}', UpdateRequestAction::class);
    });

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

    // Workflow Routing (Uses Dependency Injection)
    $app->group('/workflow', function (Group $group) use ($container) {
        $workflowService = $container->get(WorkflowService::class);

        $group->post(
            '/submit/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->submitRequest((int)$args['id'], 'submitted');
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/approve/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->approveRequest((int)$args['id'], 'approved');
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/reject/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->rejectRequest((int)$args['id'], 'rejected');
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/order/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->orderRequest((int)$args['id'], 'ordered');
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );

        $group->post(
            '/complete/{id}',
            function (Request $request, Response $response, array $args) use ($workflowService) {
                $result = $workflowService->completeRequest((int)$args['id'], 'completed');
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }
        );
    });
};
