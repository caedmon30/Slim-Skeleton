<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Approval;

use App\Domain\Approval\Approval;
use App\Domain\Approval\ApprovalNotFoundException;
use App\Domain\Approval\ApprovalRepository;
use Nette\Database\Connection;

class DatabaseApprovalRepository implements ApprovalRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {

        $this->connection = $connection;

    }

    public function findAll(): array
    {
        return array_values($this->connection->query("SELECT * FROM approvals")->fetchAll());
    }

    /**
     * @throws ApprovalNotFoundException
     */
    public function findApprovalOfId(int $id): array
    {
        if (!isset($this->approvals[$id])) {
            throw new ApprovalNotFoundException();
        }
        $this->connection->query('SELECT FROM approvals WHERE id = ?', $id);
        return array_values($this->connection->query("SELECT * FROM approvals")->fetchAll());
    }

    /**
     * @throws ApprovalNotFoundException
     */
    public function deleteApprovalOfId(int $id): array
    {
        if (!isset($this->approvals[$id])) {
            throw new ApprovalNotFoundException();
        }

        $this->connection->query('DELETE FROM approvals WHERE id = ?', $id);
        return array_values($this->connection->query("SELECT * FROM approvals")->fetchAll());
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
        return array_values($this->connection->query("SELECT * FROM approvals")->fetchAll());
    }

    public function createApproval(array $data): array
    {
        $this->connection->query('INSERT INTO approvals ?', $data);
        return array_values($this->connection->query("SELECT * FROM approvals")->fetchAll());
    }
}
