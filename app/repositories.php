<?php

declare(strict_types=1);

use App\Domain\Approval\ApprovalRepository;
use App\Domain\Employee\EmployeeRepository;
use App\Domain\Key\KeyRepository;
use App\Domain\Request\RequestRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Approval\DatabaseApprovalRepository;
use App\Infrastructure\Persistence\Employee\DatabaseEmployeeRepository;
use App\Infrastructure\Persistence\Key\DatabaseKeyRepository;
use App\Infrastructure\Persistence\Request\DatabaseRequestRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(DatabaseUserRepository::class),
        EmployeeRepository::class => autowire(DatabaseEmployeeRepository::class),
        RequestRepository::class => autowire(DatabaseRequestRepository::class),
        ApprovalRepository::class => autowire(DatabaseApprovalRepository::class),
        KeyRepository::class => autowire(DatabaseKeyRepository::class),
    ]);
};
