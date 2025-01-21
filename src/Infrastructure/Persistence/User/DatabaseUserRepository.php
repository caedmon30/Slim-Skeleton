<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Nette\Database\Connection;

class DatabaseUserRepository implements UserRepository
{
    /**
     * @tmp User[]
     */
    private array $users;
    private Connection $connection;

    public function __construct(Connection $connection, array $users = null)
    {

        $this->connection = $connection;

        $results = $this->connection->query("SELECT id, username, firstName, lastName, emailAddress FROM users");

        foreach ($results as $row) {
            $users[(int)$row['id']] = new User(
                (int)$row['id'],
                $row['username'],
                $row['firstName'],
                $row['lastName'],
                $row['emailAddress']
            );
        }
        $this->users = $users;
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
        $this->connection->delete('users', ['id' => $id]);
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
        $this->connection->update(
            'users',
            ['username' => $data['username'], 'firstName' => $data['firstName'], 'lastName' => $data['lastName'],
                'emailAddress' => $data['emailAddress']],
            ['id' => $id]
        );
        return array_values($this->users);
    }

    public function createUser(array $data): array
    {

        $this->connection->insert(
            'users',
            ['username' => $data['username'], 'firstName' => $data['firstName'], 'lastName' => $data['lastName'],
                'emailAddress' => $data['emailAddress']]
        );
        return array_values($this->users);
    }
}
