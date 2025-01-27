<?php

declare(strict_types=1);

namespace App\Domain\Request;

interface RequestRepository
{
    /**
     * @return Request[]
     */
    public function findAll(): array;

    public function findRequestOfId(int $id): Request;

    public function deleteRequestOfId(int $id): array;

    public function updateRequestOfId(int $id, array $data): array;

    public function createRequest(array $data): array;
}
