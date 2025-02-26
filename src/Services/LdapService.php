<?php
declare(strict_types=1);

namespace App\Services;

class LdapService
{
    private array $ldapConfig;

    public function __construct(array $ldapConfig)
    {
        $this->ldapConfig = $ldapConfig;
    }

    public function getUserDetails(string $username): ?array
    {
        $connection = ldap_connect($this->ldapConfig['host'], $this->ldapConfig['port']);
        if (!$connection) {
            return null;
        }
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);

        $searchFilter = sprintf("(%s=%s)", $this->ldapConfig['username_attribute'], ldap_escape($username, "", LDAP_ESCAPE_FILTER));
        $attributes = ['mail', 'sn', 'givenName', 'cn', 'telephoneNumber'];
        $search = ldap_search($connection, $this->ldapConfig['base_dn'], $searchFilter, $attributes);
        $entries = ldap_get_entries($connection, $search);
        ldap_unbind($connection);

        return is_array($entries) && isset($entries['count']) && (int) $entries['count'] > 0 ? $entries[0] : null;

    }
}