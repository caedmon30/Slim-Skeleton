<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use App\Services\WorkflowLogger;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use Psr\Http\Message\ResponseInterface as Response;


class UpdateRequestAction extends RequestAction
{
    protected ApprovalRepository $approvalRepository;
    protected LoggerInterface $logger;
    protected WorkflowLogger $workflowLogger;
    protected RequestRepository $requestRepository;

    protected ServerRequestInterface $request;

    public function __construct(LoggerInterface $logger, RequestRepository $requestRepository, ApprovalRepository $approvalRepository, WorkflowLogger $workflowLogger) {
        $this->logger = $logger;
        $this->workflowLogger = $workflowLogger;
        $this->requestRepository = $requestRepository;
        $this->approvalRepository = $approvalRepository;
        parent::__construct($this->logger, $this->requestRepository);
    }
    protected function action(): Response
    {
        $requestId = (int) $this->resolveArg('id');
        $data =  $this->request->getParsedBody();
        $this->requestRepository->updateRequestOfId($requestId, $data);

        $approver = [
            'request_id' => $requestId,
            'approver_id' => $_SESSION['username'],
            'status' => ucfirst($data['status'])
        ];
        $this->approvalRepository->createApproval($approver);
        $this->workflowLogger->logAction($requestId,$_SESSION['username'], 'Submitted',$approver['status']);
        $this->logger->info("Request ID: `{$requestId}` updated!");

        return $this->response->withHeader('HX-Redirect', '/thank-you');
    }
}
