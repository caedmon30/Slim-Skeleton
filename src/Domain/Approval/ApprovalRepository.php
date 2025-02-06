<?php

declare(strict_types=1);

namespace App\Domain\Approval;

interface ApprovalRepository
{
    /**
     * @return Approval[]
     */
    public function findAll(): array;

    public function findApprovalOfId(int $id): Approval;

    public function deleteApprovalOfId(int $id): array;

    public function updateApprovalOfId(int $id, array $data): array;

    public function createApproval(array $data): array;
}
