<?php

namespace App\Middleware;

use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use phpCAS;
class CasAuthenticationMiddleware
{
    /**
     * Invoke middleware callable.
     *
     * @param Request $request PSR-7 request
     * @param Handler $handler PSR-15 request handler
     * @param SettingsInterface $casConfig
     */

    private SettingsInterface $casConfig;

    public function __invoke(Request $request, Handler $handler, SettingsInterface $casConfig): Response
    {
        $this->casConfig = $casConfig['cas'];
        // Enable debugging
        phpCAS::setLogger();
        // Enable verbose error messages. Disable in production!
        phpCAS::setVerbose(true);
        // Initialize phpCAS
        phpCAS::client(CAS_VERSION_2_0, $this->casConfig['cas_host'], $this->casConfig['cas_port'], $this->casConfig['cas_context'], $this->casConfig['service_base_url']);

        // For production use set the CA certificate that is the issuer of the cert
        // on the CAS server and uncomment the line below
        // phpCAS::setCasServerCACert($this->casConfig['cas_server_ca_cert_path']);

        // For quick testing you can disable SSL validation of the CAS server.
        // THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
        // VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
        phpCAS::setNoCasServerValidation();

        // force CAS authentication
        phpCAS::forceAuthentication();

        // Check if phpCAS is authenticated
        if (phpCAS::isAuthenticated()) {
            $username = phpCAS::getUser();

            if ($username) {
                $_SESSION['cas_username'] = $username; // Store CAS username in session
                $_SESSION['isAuthenticatedByCAS'] = true; // Flag indicating CAS authentication
                // Optionally, session_regenerate_id(true); after successful login - consider in real app
            } else {
                // phpCAS::getUser() returned empty username after isAuthenticated() - unexpected state
                $_SESSION['isAuthenticatedByCAS'] = false;
                error_log("phpCAS::getUser() returned empty username after successful authentication.");
            }
        } else {
            // phpCAS is NOT authenticated.
            $_SESSION['isAuthenticatedByCAS'] = false;
            // For optional forced CAS authentication for all routes protected by this middleware:
            phpCAS::forceAuthentication(); // Uncomment to force CAS login
        }

        // Process the request further down the middleware stack
        return $handler->handle($request);
    }
}