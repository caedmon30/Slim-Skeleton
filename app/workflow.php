<?php

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use http\Env\Response as Response;
use App\Services\WorkflowService;

return function (App $app) {
    $container = $app->getContainer();
    $workflowService = $container->get(WorkflowService::class);

    $app->group('/workflow', function () use ($app, $workflowService) {

        // Submit a request (User moves from draft → submitted)
        $app->post('/submit/{id}', function (Request $request, Response $response, array $args) use ($workflowService) {

            return $response->withJson($workflowService->submitRequest($args['id']));
        });

        // Approve a request (Approver moves from submitted → approved)
        $app->post('/approve/{id}', function (Request $request, Response $response, array $args) use ($workflowService) {
            return $response->withJson($workflowService->approveRequest($args['id']));
        });

        // Reject a request (Approver moves from submitted → rejected)
        $app->post('/reject/{id}', function (Request $request, Response $response, array $args) use ($workflowService) {
            return $response->withJson($workflowService->rejectRequest($args['id']));
        });

        // Mark as ordered (Key Manager moves from approved → ordered)
        $app->post('/order/{id}', function (Request $request, Response $response, array $args) use ($workflowService) {
            return $response->withJson($workflowService->orderRequest($args['id']));
        });

        // Complete the process (Key Manager moves from ordered → completed)
        $app->post('/complete/{id}', function (Request $request, Response $response, array $args) use ($workflowService) {
            return $response->withJson($workflowService->completeRequest($args['id']));
        });

    });
};
