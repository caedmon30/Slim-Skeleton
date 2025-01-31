<?php

declare(strict_types=1);

namespace App\Services;

use Nette\Database\Connection;

class WorkflowLogger
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function logAction(int $requestId, string $user, string $previousState, string $newState): void
    {
        $this->connection->query(
            "INSERT INTO workflow_logs (request_id, user, previous_state, new_state) VALUES (?, ?, ?, ?)",
            $requestId,
            $user,
            $previousState,
            $newState
        );
    }

    public function getLogs(): array
    {
        return $this->connection->query("SELECT request_id, user, previous_state, new_state, timestamp 
                                             FROM workflow_logs")->fetchAll();
    }
}
