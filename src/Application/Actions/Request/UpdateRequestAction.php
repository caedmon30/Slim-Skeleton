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
        $old_data = $this->requestRepository->findRequestOfId($requestId);
        $this->requestRepository->updateRequestOfId($requestId, $data);

        // Define workflow transitions
        $transitions = [
            'Draft' => ['Submitted'],
            'Submitted' => ['Approved', 'Rejected'],
            'Approved' => ['Ordered', 'Rejected'],
            'Ordered' => ['Completed'],
            'Rejected' => ['Draft'], // Allows restarting rejected requests
        ];
// Get the allowed previous states based on the transition map
        $previousStates = $this->getPreviousStates($transitions, $old_data['status']);

        if (empty($previousStates)) $this->logger->info("No valid previous states for ".$old_data['status']);
          $currentState = $old_data['status'];
          $targetState = ucfirst($data['status']);
        // If a target state is provided, ensure it's valid
        if ($targetState !== null) {
            if (!in_array($targetState, $previousStates, true)) {
                $this->logger->info("Invalid transition to '$targetState' from '$currentState'.");
            }
        } else {
            // Default to the first valid previous state
            $targetState = $previousStates[0];
        }
        $approver = [
            'request_id' => $requestId,
            'approver_id' => $_SESSION['username'],
            'status' => ucfirst($data['status'])
        ];
        $this->approvalRepository->createApproval($approver);
        $this->workflowLogger->logAction($requestId,$_SESSION['username'], $currentState, $targetState);
        $this->logger->info("Request ID: `{$requestId}` updated!");

        return $this->response->withHeader('HX-Redirect', '/confirmation');
    }
    protected function getPreviousStates($transitions, $currentState): array
    {
        $previousStates = [];

        foreach ($transitions as $state => $nextStates) {
            if (in_array($currentState, $nextStates, true)) {
                $previousStates[] = $state;
            }
        }

        return $previousStates; // Can return multiple previous states
    }
}
