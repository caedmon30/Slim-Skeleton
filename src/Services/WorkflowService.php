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
        return $this->updateStatus($id, 'Submitted');
    }

    public function approveRequest(int $id): array
    {
        return $this->updateStatus($id, 'Approved');
    }

    public function rejectRequest(int $id): array
    {
        return $this->updateStatus($id, 'Rejected');
    }

    public function orderRequest(int $id): array
    {
        return $this->updateStatus($id, 'Ordered');
    }

    public function completeRequest(int $id): array
    {
        return $this->updateStatus($id, 'Completed');
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
