<?php
declare(strict_types=1);
namespace App\Services;

use phpCAS;


class CasService
{
    protected array $casConfig;

    public function __construct(array $casConfig)
    {
        $this->casConfig = $casConfig;
        phpCAS::client(CAS_VERSION_2_0, $casConfig['cas_host'], $casConfig['cas_port'], $casConfig['cas_context'], $casConfig['service_base_url']);
        phpCAS::setNoCasServerValidation();
    }

    public function authenticate(): bool
    {
        phpCAS::forceAuthentication();
        return phpCAS::isAuthenticated();
    }

    public function getUser(): ?string
    {
        return phpCAS::isAuthenticated() ? phpCAS::getUser() : null;
    }
}
