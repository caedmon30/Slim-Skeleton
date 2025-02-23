<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Controllers\EmailController;
use Psr\Log\LoggerInterface;
use App\Services\WorkflowLogger;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use Psr\Http\Message\ResponseInterface as Response;

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
        $justification = $data['justification'];
        $data['submitted_by'] = $_SESSION['username'];
        $submitter= $_SESSION['full_name'];
        $data['card_access'] = serialize($data['card_access']);
        $approver_id = strtok($data['pi_email'], '@');
        $data['status'] = ($approver_id == $data['submitted_by']) ? 'Approved' : 'Submitted';
        $request = $this->requestRepository->createRequest($data);

        $approver = [
            'request_id' => $request,
            'approver_id' => $approver_id,
            'status' => $data['status']
        ];

        $data['email'] = $data['email'] ?? $_SESSION['email'];
        $data['subject'] = "Order Confirmation # $request";
        $data['body'] = "<h3>New Key/Card Activation Request: #". $request. "</h3>";
        $data['body'] .= "<p>This message is to notify you that a new card activation/key request form was submitted by ".$submitter." on ".date("F j, Y, g:i a")."</p>";
        $data['body'] .= "<p>$justification</p>";
        $data['body'] .= "<p>In progress requests will be reviewed within 48 hours during working days</p>";
        $data['body'] .= "<p>Chemistry and Biochemistry,<br> KeyKeeper Application</p>";
        $this->approvalRepository->createApproval($approver);
        $this->workflowLogger->logAction($request,$data['submitted_by'], 'Draft',$data['status']);

        $this->emailController->sendEmail($data);

        $this->logger->info("New request created!");
        return $this->response->withHeader('HX-Redirect', '/thank-you');
    }
}
