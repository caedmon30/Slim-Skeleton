<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Request;

use App\Domain\Request\Request;
use App\Domain\Request\RequestNotFoundException;
use App\Domain\Request\RequestRepository;
use Nette\Database\Connection;

class DatabaseRequestRepository implements RequestRepository
{
    private array $requests;
    private Connection $connection;

    public function __construct(Connection $connection, array|null $requests = null)
    {

        $this->connection = $connection;

        $results = $this->connection->query("SELECT * FROM tblempstatus");

        foreach ($results as $row) {
            $requests[(int)$row['id']] = new Request(
                (int)$row->id,
                $row->first_name,
                $row->last_name,
                $row->email,
                $row->uid,
                $row->telephone,
                $row->extension,
                $row->pi_supervisor,
                $row->pi_email,
                $row->employment_status,
                $row->request_reason,
                $row->room_one,
                $row->room_two,
                $row->room_three,
                $row->room_four,
                $row->room_five,
                $row->card_access,
                $row->signed,
                $row->justification,
                $row->submitted_by
            );
        }
        $this->requests = $requests;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {

        return array_values($this->requests);
    }

    public function findRequestOfId(int $id): Request
    {
        if (!isset($this->requests[$id])) {
            throw new RequestNotFoundException();
        }

        return $this->requests[$id];
    }

    /**
     * @throws RequestNotFoundException
     */
    public function deleteRequestOfId(int $id): array
    {
        if (!isset($this->requests[$id])) {
            throw new RequestNotFoundException();
        }

        $this->connection->query('DELETE FROM requests WHERE id = ?', $id);
        return array_values($this->requests);
    }


    /**
     * @throws RequestNotFoundException
     */
    public function updateRequestOfId(int $id, array $data): array
    {

        if (!isset($this->requests[$id])) {
            throw new RequestNotFoundException();
        }
        $this->connection->query('UPDATE requests SET ? WHERE id = ?', $data, $id);
        return array_values($this->requests);
    }

    public function createRequest(array $data): array
    {
        $this->connection->query('INSERT INTO requests ?', $data);
        return array_values($this->requests);
    }
}
