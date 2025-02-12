<?php

declare(strict_types=1);

namespace App\Domain\Request;

interface RequestRepository
{
    public function findAll(): array;

    public function findRequestOfId(int $id): array;

    public function deleteRequestOfId(int $id): array;

    public function updateRequestOfId(int $id, array $data): int;

    public function createRequest(array $data): int;
}
