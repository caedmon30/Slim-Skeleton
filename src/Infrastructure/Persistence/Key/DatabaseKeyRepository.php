<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Key;

use App\Domain\Key\Key;
use App\Domain\Key\KeyNotFoundException;
use App\Domain\Key\KeyRepository;
use Nette\Database\Connection;

class DatabaseKeyRepository implements KeyRepository
{

    private array $keys;
    private Connection $connection;

    public function __construct(Connection $connection, array|null $keys = null)
    {

        $this->connection = $connection;

        $results = $this->connection->query("SELECT * FROM tblkeys ORDER BY", ['id' => false,]);

        foreach ($results as $row) {
            $keys[(int)$row['id']] = new Key(
                (int)$row->id,
                $row->lastName,
                $row->firstName,
                $row->campusUid,
                $row->empStatus,
                $row->keyNumber,
                $row->keyCore,
                $row->hookNumber,
                $row->roomNumber,
                $row->wingBldg,
                $row->dateCheckedIn,
                $row->dateCheckedOut,
                $row->addNotes
            );
        }
        $this->keys = $keys;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {

        return array_values($this->keys);
    }


    /**
     * @throws KeyNotFoundException
     */
    public function findKeyOfId(int $id): Key
    {
        if (!isset($this->keys[$id])) {
            throw new KeyNotFoundException();
        }

        return $this->keys[$id];
    }

    /**
     * @throws KeyNotFoundException
     */
    public function deleteKeyOfId(int $id): array
    {
        if (!isset($this->keys[$id])) {
            throw new KeyNotFoundException();
        }

        $this->connection->query('DELETE FROM tblkeys WHERE id = ?', $id);
        return array_values($this->keys);
    }

    /**
     * @throws KeyNotFoundException
     */

    public function updateKeyOfId(int $id, array $data): array
    {

        if (!isset($this->keys[$id])) {
            throw new KeyNotFoundException();
        }
        $this->connection->query('UPDATE tblkeys SET ? WHERE id = ?', $data, $id);
        return array_values($this->keys);
    }

    public function createKey(array $data): array
    {
        $this->connection->query('INSERT INTO tblkeys ?', $data);
        return array_values($this->keys);
    }
}
