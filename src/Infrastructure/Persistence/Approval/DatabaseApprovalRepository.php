<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Approval;

use App\Domain\Approval\Approval;
use App\Domain\Approval\ApprovalNotFoundException;
use App\Domain\Approval\ApprovalRepository;
use Nette\Database\Connection;

class DatabaseApprovalRepository implements ApprovalRepository
{
    private array $approvals;
    private Connection $connection;

    public function __construct(Connection $connection, array $approvals=[])
    {

        $this->connection = $connection;

        $results = $this->connection->query("SELECT * FROM approvals");

        foreach ($results as $row) {
            $approvals[(int)$row['id']] = new Approval(
                (int)$row->id,
                $row->request_id,
                $row->approver_id,
                $row->status,
                $row->created_at,
            );
        }
        $this->approvals = $approvals;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {

        return array_values($this->approvals);
    }

    /**
     * @throws ApprovalNotFoundException
     */
    public function findApprovalOfId(int $id): Approval
    {
        if (!isset($this->approvals[$id])) {
            throw new ApprovalNotFoundException();
        }

        return $this->approvals[$id];
    }

    public function deleteApprovalOfId(int $id): array
    {
        if (!isset($this->approvals[$id])) {
            throw new ApprovalNotFoundException();
        }

        $this->connection->query('DELETE FROM approvals WHERE id = ?', $id);
        return array_values($this->approvals);
    }


    /**
     * @throws ApprovalNotFoundException
     */
    public function updateApprovalOfId(int $id, array $data): array
    {

        if (!isset($this->approvals[$id])) {
            throw new ApprovalNotFoundException();
        }
        $this->connection->query('UPDATE approvals SET ? WHERE id = ?', $data, $id);
        return array_values($this->approvals);
    }

    public function createApproval(array $data): array
    {
        $this->connection->query('INSERT INTO approvals ?', $data);
        return array_values($this->approvals);
    }
}
