<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use Psr\Http\Message\ResponseInterface as Response;


class CreateApprovalAction extends ApprovalAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $approval = $this->approvalRepository->createApproval($data);
        $this->logger->info("New approval created!");

        return $this->respondWithData($approval);
    }
}
