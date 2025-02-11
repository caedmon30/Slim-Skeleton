<?php

declare(strict_types=1);

use App\Application\Actions\Approvals\CreateApprovalAction;
use App\Application\Actions\Approvals\ListApprovalAction;
use App\Application\Actions\Approvals\UpdateApprovalAction;
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
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer(); // Get DI container

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response; // CORS Pre-Flight OPTIONS Request Handler
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

    $app->group('/api/approval', function (Group $group) {
        $group->get('', ListApprovalAction::class);
        $group->post('', CreateApprovalAction::class);
        $group->get('/{id}', ViewRequestAction::class);
        $group->delete('/{id}', DeleteRequestAction::class);
        $group->put('/{id}', UpdateApprovalAction::class);
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

};
