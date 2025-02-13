<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Log\LoggerInterface;
use App\Services\WorkflowLogger;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use Psr\Http\Message\ResponseInterface as Response;

class CreateRequestAction extends RequestAction
{
    protected ApprovalRepository $approvalRepository;
    protected LoggerInterface $logger;
    protected WorkflowLogger $workflowLogger;
    protected RequestRepository $requestRepository;
    public function __construct(LoggerInterface $logger, RequestRepository $requestRepository, ApprovalRepository $approvalRepository, WorkflowLogger $workflowLogger) {
        $this->logger = $logger;
        $this->workflowLogger = $workflowLogger;
        $this->requestRepository = $requestRepository;
        $this->approvalRepository = $approvalRepository;
        parent::__construct($this->logger, $this->requestRepository);
    }
    protected function action(): Response
    {
        $data = $this->getFormData();
        $data['submitted_by'] = 'cwalters';
        $data['card_access'] = serialize($data['card_access']);
        $request = $this->requestRepository->createRequest($data);
        $approver = [
            'request_id' => $request,
            'approver_id' => strtok($data['pi_email'], '@'),
            'status' => 'Submitted'
        ];

        $this->approvalRepository->createApproval($approver);
        $this->workflowLogger->logAction($request,$approver['approver_id'], 'Draft','Submitted');
        $this->logger->info("New request created!");
        return $this->response->withHeader('HX-Redirect', '/thank-you');
    }
}
