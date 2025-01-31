<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;

class LdapDetailsMiddleware implements MiddlewareInterface
{
    private array $allowedRoles;
    private string $ldapServer = "ldap://ldap.example.com";
    private string $ldapBaseDn = "ou=users,dc=example,dc=com";

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            throw new HttpForbiddenException($request, "User not authenticated");
        }

        $userRole = $this->fetchUserRoleFromLdap($user);
        if (!in_array($userRole, $this->allowedRoles, true)) {
            throw new HttpForbiddenException($request, "Access denied");
        }

        return $handler->handle($request);
    }

    private function fetchUserRoleFromLdap(string $username): ?string
    {
        $ldapConn = ldap_connect($this->ldapServer);
        if (!$ldapConn) {
            return null;
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $search = ldap_search($ldapConn, $this->ldapBaseDn, "(uid=$username)");
        $entries = ldap_get_entries($ldapConn, $search);
        return $entries[0]['role'][0] ?? null;
    }
}
