<?php

declare(strict_types=1);

namespace App\Domain\Key;

interface KeyRepository
{
    /**
     * @return Key[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Key
     * @throws KeyNotFoundException
     */

    public function findKeyOfId(int $id): Key;

    public function deleteKeyOfId(int $id): array;

    public function updateKeyOfId(int $id, array $data): array;

    public function createKey(array $data): array;
}
