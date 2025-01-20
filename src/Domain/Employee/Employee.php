<?php

namespace App\Domain\Employee;

use JsonSerializable;

class Employee implements JsonSerializable
{
    private ?int $id;

    private string $empStatusName;

    public function __construct(?int $id, string $empStatusName)
    {
        $this->id = $id;
        $this->empStatusName = ucfirst($empStatusName);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmpStatusName(): string
    {
        return $this->empStatusName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'empStatusName' => $this->empStatusName,
        ];
    }
}
