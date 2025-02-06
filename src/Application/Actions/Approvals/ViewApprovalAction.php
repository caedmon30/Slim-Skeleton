<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use Psr\Http\Message\ResponseInterface as Response;

class ViewApprovalAction extends ApprovalAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $approvalId = (int) $this->resolveArg('id');
        $approval = $this->approvalRepository->findApprovalOfId($approvalId);
        $this->logger->info("Request of id `{$approvalId}` was viewed.");

        return $this->respondWithData($approval);
    }
}
