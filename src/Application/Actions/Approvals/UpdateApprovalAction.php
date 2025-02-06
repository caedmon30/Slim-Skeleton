<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateApprovalAction extends ApprovalAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $approval_id = (int) $this->resolveArg('id');
        $data = $this->getFormData();
        $approver = $this->approvalRepository->updateApprovalOfId($approval_id, $data);
        $this->logger->info("Approver id `{$approval_id}` was updated.");

        return $this->respondWithData($approver);
    }
}
