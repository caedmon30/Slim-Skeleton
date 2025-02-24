<?php

namespace App\Middleware;

use App\Application\Settings\SettingsInterface;
use LDAP\Connection;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;
use Slim\Routing\RouteContext;

// For general exceptions

// For configuration related exceptions

class LdapAuthorizationMiddleware
{
    /**
     * LDAP Configuration (Ideally, fetch from config service/container)
     */
    /**
     * Invoke middleware callable.
     *
     * @param Request $request PSR-7 request
     * @param Handler $handler PSR-15 request handler
     * @param SettingsInterface $ldapConfig
     * @return Response
     */
    private SettingsInterface $ldapConfig;

    public function __invoke(Request $request, Handler $handler, SettingsInterface $ldapConfig): Response
    {
        global $response;
        $isAuthorized = false;
        $casUsername = $_SESSION['cas_username'] ?? null; // Get CAS username from session
        $this->ldapConfig = $ldapConfig['ldap'];

        if (!empty($casUsername) && $_SESSION['isAuthenticatedByCAS'] === true) { // Check CAS authentication first
            try {
                if ($this->connectToLdap()) {
                    $ldapConn = $this->connectToLdap();
                    $ldapUserDn = $this->getUserDn($ldapConn, $casUsername);
                    if ($ldapUserDn) {

                        // **Authorization Logic based on LDAP - Check if user DN is found**
                        $isAuthorized = ($ldapUserDn != false);

                        if ($isAuthorized) {
                            $_SESSION['isAuthorizedByLDAP'] = true; // Flag LDAP authorization success

                            // Fetch and store user details from LDAP in session for later use in application.
                            $user_details = $this->fetchUserDetails($casUsername);

                            $_SESSION['email'] = $user_details['email'];
                            $_SESSION['firstname'] = $user_details['firstName'];
                            $_SESSION['lastname'] = $user_details['lastName'];
                            $_SESSION['uid'] = $user_details['uid'];
                            $_SESSION['telephonenumber'] = $user_details['telephonenumber'];
                            $_SESSION['department'] = $user_details['department'];

                        } else {
                            $_SESSION['isAuthorizedByLDAP'] = false;
                            // Log authorization failure
                            error_log("LDAP Authorization failed for CAS user: " . $casUsername . ". User not authorized based on LDAP rules.");
                        }
                    } else {
                        // User DN not found in LDAP - consider unauthorized and redirect to homepage
                        $_SESSION['isAuthorizedByLDAP'] = false;
                        error_log("LDAP User DN not found for CAS user (LDAP Authorization failed): " . $casUsername);
                        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                        $url = $routeParser->urlFor('home');

                        return $response
                            ->withHeader('Location', $url)
                            ->withStatus(302);
                    }
                    ldap_close($ldapConn);
                } else {
                    // LDAP Connection failed - consider unauthorized
                    $_SESSION['isAuthorizedByLDAP'] = false; // Set to false due to LDAP error
                }
            } catch (Exception $e) {
                // Exception during LDAP authorization process
                $_SESSION['isAuthorizedByLDAP'] = false; // Set to false due to exception
                error_log("LDAP Authorization Exception for CAS user: " . $casUsername . ". Exception: " . $e->getMessage());
            }
        } else {
            // Not authenticated by CAS - consider unauthorized by LDAP as well.
            $_SESSION['isAuthorizedByLDAP'] = false;
            // If CAS authentication is required for authorization, you might want to handle unauthenticated users here
            // e.g., redirect to CAS login or return a 403/401 response.
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('404');

            return $response
                ->withHeader('Location', $url)
                ->withStatus(302);
        }

        // Set a general 'loggedIn' flag based on both CAS authentication and LDAP authorization
        $_SESSION['loggedIn'] = ($_SESSION['isAuthorizedByLDAP'] === true);

        if (!$isAuthorized && ($_SESSION['loggedIn'] !== true)) { // If *not* authorized by LDAP and not ultimately 'loggedIn'
            // **Example: Return a 403 Forbidden response if not authorized**
            $response = $response->withStatus(403);
            $response->getBody()->write('<h1>Forbidden</h1><p>You are not authorized to access this resource.</p>');
            return $response; // Stop middleware chain and return 403
        }


        // Process the request further down the middleware stack if authorized
        return $handler->handle($request);
    }

    /**
     * Connects to the LDAP server. (Same as before)
     *
     * @return Connection LDAP link identifier on success, false on error.
     */
    private function connectToLdap(): Connection
    {
        $ldapUri = $this->ldapConfig['host'].':'.$this->ldapConfig['port'];
        $ldapConn = ldap_connect($ldapUri);

        if (!$ldapConn) {
            error_log("LDAP Connection failed to {$ldapUri}");
            die("That LDAP-URI was not parseable");
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3); // Use LDAPv3
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0); // Disable referrals (adjust if needed)

        return $ldapConn;
    }

    /**
     * Retrieves the Distinguished Name (DN) of a user based on the username. (Same as before)
     *
     * @param Connection $ldapConn LDAP link identifier.
     * @param string $username The username to search for.
     * @return string|false User DN on success, false if not found or error.
     */
    private function getUserDn(Connection $ldapConn, string $username): false|string
    {
        $baseDn = $this->ldapConfig['base_dn'];
        $usernameAttribute = $this->ldapConfig['username_attribute'];

        $searchFilter = "({$usernameAttribute}={$username})"; // e.g., (uid=username)

        $searchResult = @ldap_search($ldapConn, $baseDn, $searchFilter, ['dn']); // Search for DN only
        if (!$searchResult) {
            error_log("LDAP Search failed for user '{$username}'. LDAP Error: " . ldap_error($ldapConn));
            return false; // Search failed
        }

        $entryCount = ldap_count_entries($ldapConn, $searchResult);
        if ($entryCount !== 1) {
            return false; // User not found or not unique
        }

        $entry = ldap_first_entry($ldapConn, $searchResult);
        if (!$entry) {
            error_log("LDAP First entry retrieval failed for user '{$username}'. LDAP Error: " . ldap_error($ldapConn));
            return false; // Failed to get entry
        }

        $userDn = ldap_get_dn($ldapConn, $entry);
        if (!$userDn) {
            error_log("LDAP DN retrieval failed for user entry. LDAP Error: " . ldap_error($ldapConn));
            return false; // Failed to get DN
        }

        return $userDn; // Return the user's DN
    }

    private function fetchUserDetails(string $username): null | array
    {
        $ldapUri = $this->ldapConfig['host'].':'.$this->ldapConfig['port'];
        $ldapConn = ldap_connect($ldapUri);
        if (!$ldapConn) {
            return null;
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $search = ldap_search($ldapConn, $this->ldapConfig['base_dn'], "(uid=$username)");
        $entries = ldap_get_entries($ldapConn, $search);

        $user_info = $entries[0];
        $user_info["email"] = $entries[0]["mail"][0];
        $user_info["firstName"] = $entries[0]["givenname"][0];
        $user_info["lastName"] = $entries[0]["sn"][0];
        $user_info["uid"] = $entries[0]["employeenumber"][0];
        $user_info["telephonenumber"] = $entries[0]["telephonenumber"][0];
        $user_info["department"] = $entries[0]["ou"][0];

        return $user_info;
    }

}
