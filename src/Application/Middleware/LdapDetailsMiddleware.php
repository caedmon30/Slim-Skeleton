<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;

class LdapDetailsMiddleware implements MiddlewareInterface
{
    private string $allowedUser;
    private string $ldapServer = "ldap://ldap.umd.edu";
    private string $ldapBaseDn = "ou=people,dc=umd,dc=edu";

    public function __construct(string $allowedUser)
    {
        $this->allowedUser = $allowedUser;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $allowedUser = $request->getAttribute('user');
        if (! $allowedUser) {
            throw new HttpForbiddenException($request, "User not authenticated");
        }

        $userDetails = $this->fetchUserDetailsFromLdap($allowedUser);
        if (!in_array($this->allowedUser,$userDetails,true)) {
            throw new HttpForbiddenException($request, "Access denied");
        }

        return $handler->handle($request);
    }

    private function fetchUserDetailsFromLdap(string $username): null | array
    {
        $ldapConn = ldap_connect($this->ldapServer);
        if (!$ldapConn) {
            return null;
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $search = ldap_search($ldapConn, $this->ldapBaseDn, "(uid=$username)");
        $entries = ldap_get_entries($ldapConn, $search);

        $user_info = $entries[0];
        $user_info["email"] = $entries[0]["mail"][0];
        $user_info["firstname"] = $entries[0]["givenname"][0];
        $user_info["lastname"] = $entries[0]["sn"][0];
        $user_info["uid"] = $entries[0]["employeenumber"][0];
        $user_info["telephonenumber"] = $entries[0]["telephonenumber"][0];
        $user_info["department"] = $entries[0]["ou"][0];

        return $user_info;

    }
}
