<?php

namespace App\Services;

use Nette\Database\Connection;
use Exception;

class WorkflowService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function submitRequest(int $id): array
    {
        return $this->updateStatus($id, 'submitted');
    }

    public function approveRequest(int $id): array
    {
        return $this->updateStatus($id, 'approved');
    }

    public function rejectRequest(int $id): array
    {
        return $this->updateStatus($id, 'rejected');
    }

    public function orderRequest(int $id): array
    {
        return $this->updateStatus($id, 'ordered');
    }

    public function completeRequest(int $id): array
    {
        return $this->updateStatus($id, 'completed');
    }

    private function updateStatus(int $id, string $status): array
    {
        try {
            $this->connection->query("UPDATE requests SET status = ? WHERE id = ?", $status, $id);

            return ['success' => true, 'message' => "Request $id moved to $status."];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
