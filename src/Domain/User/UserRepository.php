<?php

declare(strict_types=1);

namespace App\Domain\User;

use Psr\Http\Message\ServerRequestInterface as Request;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    public function deleteUserOfId(int $id): array;

    public function updateUserOfId(int $id, array $data): array;

    public function createUser(array $data): array;

}
