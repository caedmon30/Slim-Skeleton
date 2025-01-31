<?php

declare(strict_types=1);

namespace App\Services;

use Nette\Database\Connection;

class WorkflowLogger
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function logAction(int $requestId, string $user, string $previousState, string $newState): void
    {
        $this->db->query(
            "INSERT INTO workflow_logs (request_id, user, previous_state, new_state) VALUES (?, ?, ?, ?)",
            $requestId,
            $user,
            $previousState,
            $newState
        );
    }

    public function getLogs(): array
    {
        return $this->db->query("SELECT request_id, user, previous_state, new_state FROM workflow_logs")->fetchAll();
    }
}
