<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Services\LdapService;

class LdapAuthorizationMiddleware implements MiddlewareInterface
{
    private LdapService $ldapService;

    public function __construct(LdapService $ldapService)
    {
        $this->ldapService = $ldapService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $username = $_SESSION['cas_user'] ?? null;
        if (!$username) {
            $response = new \Slim\Psr7\Response(403);
            $response->getBody()->write('Forbidden: No CAS user');
            return $response->withHeader('Content-Type', 'text/plain');


        }

        $ldapEntries = $this->ldapService->getUserDetails($username);
        if ($ldapEntries) {
            $_SESSION['ldap_user'] = [
                'email' => $ldapEntries['mail'][0] ?? null,
                'sn' => $ldapEntries['sn'][0] ?? null,
                'firstname' => $ldapEntries['givenName'][0] ?? null,
                'lastname' => $ldapEntries['cn'][0] ?? null,
                'telephoneNumber' => $ldapEntries['telephoneNumber'][0] ?? null,
            ];
            $_SESSION['role'] = 'authorized_user';
        } else {
            $_SESSION['role'] = 'user';
        }

        return $handler->handle($request);
    }
}
