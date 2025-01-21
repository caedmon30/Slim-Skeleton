<?php

declare(strict_types=1);

use App\Domain\Employee\EmployeeRepository;
use App\Domain\Key\KeyRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Employee\DatabaseEmployeeRepository;
use App\Infrastructure\Persistence\Key\DatabaseKeyRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(DatabaseUserRepository::class),
        EmployeeRepository::class => autowire(DatabaseEmployeeRepository::class),
        KeyRepository::class => autowire(DatabaseKeyRepository::class),
    ]);
};
