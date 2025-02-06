<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteApprovalAction extends ApprovalAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $approvalId = (int) $this->resolveArg('id');
        $approval = $this->approvalRepository->deleteApprovalOfId($approvalId);
        $this->logger->info("Request of id `{$approvalId}` was deleted.");

        return $this->respondWithData($approval);
    }
}
