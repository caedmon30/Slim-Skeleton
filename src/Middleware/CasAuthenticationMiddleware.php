<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Services\CasService;

class CasAuthenticationMiddleware implements MiddlewareInterface
{
    private CasService $casService;

    public function __construct(CasService $casService)
    {
        $this->casService = $casService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if ($this->casService->authenticate()) {
            $_SESSION['cas_user'] = $this->casService->getUser();
        }
        return $handler->handle($request);
    }
}