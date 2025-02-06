<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals;

use App\Application\Actions\Action;
use App\Domain\Approval\ApprovalRepository;
use Psr\Log\LoggerInterface;

abstract class ApprovalAction extends Action
{
    protected ApprovalRepository $approvalRepository;

    public function __construct(LoggerInterface $logger, ApprovalRepository $approvalRepository)
    {
        parent::__construct($logger);
        $this->approvalRepository = $approvalRepository;
    }
}
