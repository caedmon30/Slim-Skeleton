<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use phpCAS;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class CasAuthMiddleware implements MiddlewareInterface
{

    private array $casConfig = [
        'host' => 'localhost',
        'port' => 443,
        'path' => '/cas',
        'service_base_url' => 'http://localhost:8080',
    ];

    public function __construct()
    {
        // Initialize phpCAS
        phpCAS::client(CAS_VERSION_2_0, $this->casConfig['host'], $this->casConfig['port'], $this->casConfig['path'], $this->casConfig['service_base_url']);
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
