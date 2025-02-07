<?php

namespace App\Middleware;

use LDAP\Connection;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Exception; // For general exceptions
use RuntimeException;

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
     * @param Response $response PSR-7 response handler
     *
     * @return Response
     */
    private array $ldapConfig = [
        'host' => 'ldap.example.com', // Replace with your LDAP server hostname/IP
        'port' => 389,                // Typically 389 (LDAP) or 636 (LDAPS)
        'base_dn' => 'dc=example,dc=com', // Replace with your base DN
        'username_attribute' => 'uid',   // Attribute to use for username (e.g., uid, samaccountname)
        // 'authorized_group_dn' => 'cn=MyAppUsers,ou=Groups,dc=example,dc=com', // Example: DN of an authorized LDAP group (optional)
        // 'require_group_membership' => false, // Set to true if group membership is required for authorization
    ];


    public function __invoke(Request $request, Handler $handler): Response
    {
        global $response;
        $isAuthorized = false;
        $casUsername = $_SESSION['cas_username'] ?? null; // Get CAS username from session

        if (!empty($casUsername) && $_SESSION['isAuthenticatedByCAS'] === true) { // Check CAS authentication first
            try {
                $ldapConn = $this->connectToLdap();
                if ($ldapConn) {
                    $ldapUserDn = $this->getUserDn($ldapConn, $casUsername);
                    if ($ldapUserDn) {
                        // **Authorization Logic based on LDAP - Example: Just check if user DN is found**
                        // **Replace this with your actual authorization rules!**
                        // **Examples:**
                        // 1. Check for group membership:  $isAuthorized = $this->isUserMemberOfGroup($ldapConn, $ldapUserDn, $this->ldapConfig['authorized_group_dn']);
                        // 2. Check for specific user attributes: $isAuthorized = $this->checkUserAttribute($ldapConn, $ldapUserDn, 'department', 'IT');
                        // 3. For this example, just checking if user DN exists in LDAP after CAS authentication might be enough for "authorization" in a basic sense.
                        $isAuthorized = true; // For this example, simply authorize if user DN is found after CAS

                        if ($isAuthorized) {
                            $_SESSION['isAuthorizedByLDAP'] = true; // Flag LDAP authorization success
                            // Optionally, fetch and store more user details from LDAP in session for later use in application.
                        } else {
                            $_SESSION['isAuthorizedByLDAP'] = false;
                            // Optionally log authorization failure
                            // error_log("LDAP Authorization failed for CAS user: " . $casUsername . ". User not authorized based on LDAP rules.");
                        }
                    } else {
                        // User DN not found in LDAP - consider unauthorized
                        $_SESSION['isAuthorizedByLDAP'] = false;
                        // error_log("LDAP User DN not found for CAS user (LDAP Authorization failed): " . $casUsername);
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
        }

        // Set a general 'loggedIn' flag based on both CAS authentication and LDAP authorization (you can adjust this logic)
        $_SESSION['loggedIn'] = ($_SESSION['isAuthenticatedByCAS'] ?? false) && ($_SESSION['isAuthorizedByLDAP'] ?? false);

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

    /**
     * Example: Check if a user is a member of an LDAP group.
     * **Implement your actual group membership check logic here based on your LDAP schema.**
     *
     * @param resource $ldapConn LDAP link identifier.
     * @param string $userDn User's Distinguished Name.
     * @param string $groupDn Group's Distinguished Name.
     * @return bool True if user is a member, false otherwise.
     */
    private function isUserMemberOfGroup($ldapConn, $userDn, $groupDn): bool
    {
        if (empty($groupDn)) {
            return false; // Group DN not configured - consider not a member (or adjust logic)
        }

        // **Implement your LDAP group membership check here.**
        // **Example (using 'memberOf' attribute - common in Active Directory):**
        $searchFilter = "(&(objectClass=group)(distinguishedName={$groupDn})(member:1.2.840.113556.1.4.1941:={$userDn}))";

        $searchResult = @ldap_search($ldapConn, $this->ldapConfig['base_dn'], $searchFilter, ['dn']);
        if (!$searchResult) {
            error_log("LDAP Group membership search failed. LDAP Error: " . ldap_error($ldapConn));
            return false; // Search failed
        }

        $entryCount = ldap_count_entries($ldapConn, $searchResult);
        return $entryCount > 0; // User is a member if group entry is found

        // **Alternative group membership checks (depending on your LDAP schema):**
        // - Check 'groupOfNames' objectClass and 'member' attribute.
        // - Check 'posixGroup' objectClass and 'memberUid' attribute.
        // - Recursive group membership checks if nested groups are used.

        // **Important:**  LDAP group membership checks can be complex and schema-dependent.
        // Adapt the search filter and logic to match your specific LDAP schema and group structure.
    }

    /**
     * Example: Check for a specific user attribute value.
     * **Implement your actual attribute check logic here.**
     *
     * @param resource $ldapConn LDAP link identifier.
     * @param string $userDn User's Distinguished Name.
     * @param string $attributeName Attribute to check (e.g., 'department').
     * @param string $expectedValue Expected attribute value.
     * @return bool True if attribute value matches, false otherwise.
     */
    private function checkUserAttribute($ldapConn, $userDn, $attributeName, $expectedValue): bool
    {
        $readResult = @ldap_read($ldapConn, $userDn, [$attributeName]);
        if (!$readResult) {
            error_log("LDAP attribute read failed for user DN '{$userDn}'. LDAP Error: " . ldap_error($ldapConn));
            return false; // Read failed
        }

        $entry = ldap_first_entry($ldapConn, $readResult);
        if (!$entry) {
            error_log("LDAP First entry retrieval failed during attribute check for user DN '{$userDn}'. LDAP Error: " . ldap_error($ldapConn));
            return false; // Entry not found
        }

        $attributeValues = ldap_get_values($ldapConn, $entry, $attributeName);
        if ($attributeValues === false || $attributeValues['count'] < 1) {
            return false; // Attribute not found or no values
        }

        // Check if any of the attribute values match the expected value (case-insensitive example)
        for ($i = 0; $i < $attributeValues['count']; $i++) {
            if (strcasecmp($attributeValues[$i], $expectedValue) === 0) {
                return true; // Attribute value matches
            }
        }

        return false; // Attribute value not found or does not match expected value
    }
}
