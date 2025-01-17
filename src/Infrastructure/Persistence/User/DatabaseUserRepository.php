<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use MeekroDB as Connection;

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
        $this->users = $this->connection->query("SELECT id, username, firstName, lastName, emailAddress FROM users");
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
     * @throws \MeekroDBException
     * @throws UserNotFoundException
     */
    public function deleteUserOfId(int $id): array
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }
        $this->users = $this->connection->delete('users', ['id' => $id]);
        return array_values($this->users);
    }

    /**
     * @throws \MeekroDBException
     * @throws UserNotFoundException
     */

    public function updateUserOfId(int $id, array $data): array
    {

        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }
        $this->users = $this->connection->update(
            'users',
            ['username' => $data['username'], 'firstName' => $data['firstName'], 'lastName' => $data['lastName'],
                'emailAddress' => $data['emailAddress']],
            ['id' => $id]
        );
        return array_values($this->users);
    }

    public function createUser(array $data): array
    {

        $this->users = $this->connection->insert(
            'users',
            ['username' => $data['username'], 'firstName' => $data['firstName'], 'lastName' => $data['lastName'],
                'emailAddress' => $data['emailAddress']]
        );
        return array_values($this->users);
    }
}
