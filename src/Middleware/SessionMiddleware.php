<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;

class SessionMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        // Start PHP session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['id'] = $request->getAttribute('id');
        $_SESSION['username'] = 'cwalters';
        $_SESSION['full_name'] = 'Caedmon Walters';
        $_SESSION['role'] = 'admin';
        // Process the request further down the middleware stack
        return $handler->handle($request);
    }
}
