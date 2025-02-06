<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use Psr\Http\Message\ResponseInterface as Response;

class ListApprovalAction extends ApprovalAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $approvals = $this->approvalRepository->findAll();
        $this->logger->info("Approval list was viewed.");

        return $this->respondWithData($approvals);
    }
}
