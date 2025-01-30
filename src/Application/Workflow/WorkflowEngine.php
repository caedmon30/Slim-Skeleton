<?php

namespace App\Application\Workflow;

class WorkflowEngine
{
    private $states = [];
    private $transitions = [];

    public function __construct(array $states, array $transitions)
    {
        $this->states = $states;
        $this->transitions = $transitions;
    }

    public function canTransition(string $currentState, string $action): bool
    {
        return isset($this->transitions[$currentState][$action]);
    }

    public function getNextState(string $currentState, string $action): ?string
    {
        return $this->transitions[$currentState][$action] ?? null;
    }

    public function getAvailableActions(string $currentState): array
    {
        return array_keys($this->transitions[$currentState] ?? []);
    }
}
