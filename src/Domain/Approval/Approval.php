<?php

namespace App\Domain\Approval;

use JsonSerializable;

class Approval implements JsonSerializable
{
    private ?int $id;
    private int $request_id;
    private string $approver_id;
    private string $status;
    private string $created_at;


    public function __construct(
        ?int $id,
        int $request_id,
        string $approver_id,
        string $status,
        string $created_at,
    ) {
        $this->id = $id;
        $this->request_id = $request_id;
        $this->approver_id = $approver_id;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestId(): int
    {
        return $this->request_id;
    }
    /**
     * @return string
     */
    public function getApproverId(): string
    {
        return $this->approver_id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'approver_id' => $this->approver_id,
            'status' => $this->status,
            'created_at' => $this->created_at,

        ];
    }
}
