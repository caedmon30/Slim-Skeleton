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
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $data =  $this->request->getParsedBody();
        //$data = $this->getFormData();
        $decision = $data['decision'];
        $request = $this->requestRepository->updateRequestOfId($userId, $data);

        $approver = [
            'request_id' => $request,
            'approver_id' => $_SESSION['username'],
            'status' => ucfirst($decision)
        ];
        $this->approvalRepository->createApproval($approver);
        $this->workflowLogger->logAction($request,$_SESSION['username'], 'Submitted',$approver['status']);
        $this->logger->info("Request ID: `{$request}` updated!");

        return $this->response->withHeader('HX-Redirect', '/thank-you');
    }
}
