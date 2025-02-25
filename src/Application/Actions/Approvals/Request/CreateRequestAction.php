<?php

declare(strict_types=1);

namespace App\Application\Actions\Approvals\Request;

use App\Controllers\EmailController;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use App\Services\WorkflowLogger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateRequestAction extends RequestAction
{
    protected ApprovalRepository $approvalRepository;

    protected LoggerInterface $logger;
    protected EmailController $emailController;
    protected WorkflowLogger $workflowLogger;
    protected RequestRepository $requestRepository;


    public function __construct( LoggerInterface $logger, EmailController $emailController, RequestRepository $requestRepository, ApprovalRepository $approvalRepository, WorkflowLogger $workflowLogger) {

        $this->logger = $logger;
        $this->emailController = $emailController;
        $this->workflowLogger = $workflowLogger;
        $this->requestRepository = $requestRepository;
        $this->approvalRepository = $approvalRepository;
        parent::__construct($this->logger, $this->requestRepository);
    }
    protected function action(): Response
    {

        $data = $this->getFormData();

        $data['submitted_by'] = $_SESSION['username'];
        $submitter= $_SESSION['full_name'];
        $data['card_access'] = serialize($data['card_access']);
        $approver_id = strtok($data['pi_email'], '@');
        $data['status'] = ($approver_id == $data['submitted_by']) ? 'Approved' : 'Submitted';

        $request = $this->requestRepository->createRequest($data);

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
        if ($data['status'] == 'Submitted') $data['cc'][] = $data['pi_email'];
        if ($data['status'] !== 'Submitted') $data['cc'][] = 'chem-keykeeper@umd.edu';

        $approver = [
            'request_id' => $request,
            'approver_id' => $approver_id,
            'status' => $data['status']
        ];

        date_default_timezone_set('America/New_York'); // Set the default timezone to New York/GMT+4');

        /** Set up email message  */
        $data['email'] = $data['email'] ?? $_SESSION['email'];
        $data['subject'] = "Key/Card Request Confirmation # $request";
        $data['body'] = "<img src='https://chem.umd.edu/sites/default/files/UMD_CMNS_ChemBio_S1_Color_Padded.png' alt='Chemistry Key-keeper' width='300px'  style='text-align: center; margin-bottom: 10px;'>";
        $data['body'] .= "<h3>New Key/Card Activation Request: #". $request. "</h3>";
        $data['body'] .= "<p>This message is to notify you that a new card activation/key request form was submitted by ".$submitter." on ".date("F j, Y, g:i a")."</p>";
        if (isset($data['justification']))  {
        $data['body'] .= "<p>{$data['justification']}</p>";
        }
        $data['body'] .= "<p>In progress requests will be reviewed within 48 hours during working days</p>";
        $data['body'] .= "<p>Chemistry and Biochemistry,<br> KeyKeeper Application</p>";

        /** Create Approval Log  */
        $this->approvalRepository->createApproval($approver);

        /** Create Workflow Transaction Log  */
        $this->workflowLogger->logAction($request,$data['submitted_by'], 'Draft',$data['status']);

        /** Send email Notices  */
        $this->emailController->sendEmail($data);

        /** Update Activity Log File */
        $this->logger->info("New request created!");

        return $this->response->withHeader('HX-Redirect', '/thank-you');
    }
}
