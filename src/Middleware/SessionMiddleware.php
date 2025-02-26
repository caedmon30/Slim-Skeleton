<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Application\Settings\SettingsInterface;
use App\Services\CasService;
use App\Services\LdapService;

class SessionMiddleware implements MiddlewareInterface
{
    private array $sessionSettings;
    private CasService $casService;
    private LdapService $ldapService;

    public function __construct(SettingsInterface $settings, CasService $casService, LdapService $ldapService)
    {
        $this->sessionSettings = $settings->get('session');
        $this->casService = $casService;
        $this->ldapService = $ldapService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->sessionSettings['name']);
            session_set_cookie_params([
                'lifetime' => $this->sessionSettings['lifetime'],
                'path' => '/',
                'domain' => $this->sessionSettings['domain'],
                'secure' => $this->sessionSettings['secure'],
                'httponly' => $this->sessionSettings['httponly'],
            ]);

            if (!empty($this->sessionSettings['save_path'])) {
                session_save_path($this->sessionSettings['save_path']);
            }

            session_start();
        }

        // Authenticate CAS user
        if (!isset($_SESSION['cas_user'])) {
            if ($this->casService->authenticate()) {
                $casUser = $this->casService->getUser();
                $_SESSION['cas_user'] = $casUser;

                // Fetch LDAP details
                $ldapEntries = $this->ldapService->getUserDetails($casUser);
                if ($ldapEntries) {
                    $_SESSION['ldap_user'] = [
                        'sn' => $ldapEntries[0]['sn'][0] ?? null,
                        'department' => $ldapEntries[0]['department'][0] ?? null,
                        'telephoneNumber' => $ldapEntries[0]['telephonenumber'][0] ?? null,
                    ];
                }
            }
        }

        return $handler->handle($request);
    }
}

