<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Controllers\EmailController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use App\Services\WorkflowLogger;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use Psr\Http\Message\ResponseInterface as Response;


class UpdateRequestAction extends RequestAction
{
    protected ApprovalRepository $approvalRepository;
    protected EmailController $emailController;

    protected LoggerInterface $logger;
    protected WorkflowLogger $workflowLogger;
    protected RequestRepository $requestRepository;

    protected ServerRequestInterface $request;

    public function __construct(LoggerInterface $logger,  EmailController $emailController, RequestRepository $requestRepository, ApprovalRepository $approvalRepository, WorkflowLogger $workflowLogger) {
        $this->logger = $logger;
        $this->emailController = $emailController;
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

        /**
         * Sets the CC (carbon copy) recipient based on the status of the request.
         *
         * - If the status is 'Submitted', the CC email is set to the Principal Investigator's (PI) email.
         * - If the status is anything other than 'Submitted', the CC email is set to the Chemistry Key Manager.
         *
         * This ensures that:
         * - The PI is notified when a request is submitted.
         * - The Chemistry Key Manager is notified for all other statuses.
         */
        if ($old_data['email'] !== $old_data['pi_email']) $data['cc'][] = $old_data['pi_email'];
        if ($old_data['status'] !== 'Submitted') $data['cc'][] = 'chem-keykeeper@umd.edu';

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
        if (!empty($targetState)) {
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

        /** Set up email message  */
        date_default_timezone_set('America/New_York'); // Set the default timezone to New York');

        $data['subject'] = "Key/Card Request # {$requestId} - Status Notification";
        $data['body'] = "<img src='https://chem.umd.edu/sites/default/files/UMD_CMNS_ChemBio_S1_Color_Padded.png' alt='Chemistry Key-keeper' width='300px'  style='text-align: center; margin-bottom: 10px;'>";
        $data['body'] .= "<h3>Key/Card Activation Request Status Update</h3>";
        $data['body'] .= "<p>This message is to notify you that the card activation/key request task #{$requestId} was modified and has been updated to status - {$data['status']}</p>";
        $data['body'] .= "<p>Approver: ". $_SESSION['full_name']." on ".date("F j, Y, g:i a")."</p>";
        if (isset($data['justification']))  {
            $data['body'] .= "<p>{$data['justification']}</p>";
        }
        $data['body'] .= "<p>In progress requests will be reviewed within 48 hours during working days</p>";
        $data['body'] .= "<p>No further action is required for rejected requests</p>";
        $data['body'] .= "<p>Chemistry and Biochemistry,<br> KeyKeeper Application</p>";

        /** Create Approval Log  */
        $this->approvalRepository->createApproval($approver);

        /** Create Workflow Transaction Log  */
        $this->workflowLogger->logAction($requestId,$_SESSION['username'], $currentState, $targetState);

        /** Send email Notices  */
        $this->emailController->sendEmail($data);

        /** Update Activity Log File */
        $this->logger->info("Request ID: `{$requestId}` updated!");

        return $targetState == "Completed" ? $this->response->withHeader('HX-Redirect', '/request-approve/'.$requestId) : $this->response->withHeader('HX-Redirect', '/confirmation');

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
