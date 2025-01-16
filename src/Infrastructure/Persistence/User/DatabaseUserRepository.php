<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Selective\Database\Connection;

class DatabaseUserRepository implements UserRepository
{
    /**
     * @tmp User[]
     */
    private array $users;
    private Connection $connection;

    /**
     * @param User[]|null $users
     */
    public function __construct(Connection $connection, array $users = null)
    {
        $this->users = $users;
        $this->connection = $connection;
        $query = $this->connection->select()->from('users');
        $query->columns(['id', 'username', 'firstName', 'lastName', 'emailAddress']);

        $this->users =  $query->execute()->fetch() ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }

        return $this->users[$id];
    }

    /**
     * @throws UserNotFoundException
     */
    public function deleteUserOfId(int $id): array
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }
        $this->connection->delete()->from('users')->where('id', '==', $id)->execute();
        return array_values($this->users);
    }
}
