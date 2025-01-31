<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use phpCAS;

class CasAuthMiddleware implements MiddlewareInterface
{
    public function __construct()
    {
        // Initialize phpCAS
        phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context, $client_service_name, false);
        phpCAS::setNoCasServerValidation();
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!phpCAS::isAuthenticated()) {
            phpCAS::forceAuthentication();
        }

        $user = phpCAS::getUser();
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
