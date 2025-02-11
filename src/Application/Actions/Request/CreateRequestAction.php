<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Log\LoggerInterface;
use App\Domain\Approval\ApprovalRepository;
use App\Domain\Request\RequestRepository;
use Psr\Http\Message\ResponseInterface as Response;

class CreateRequestAction extends RequestAction
{
    protected ApprovalRepository $approvalRepository;
    protected LoggerInterface $logger;
    protected RequestRepository $requestRepository;
    public function __construct(LoggerInterface $logger, RequestRepository $requestRepository, ApprovalRepository $approvalRepository) {
        $this->logger = $logger;
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
        $payload = $this->requestRepository->findAll();
        $this->logger->info("New request created!");

        return $this->respondWithData($payload );
    }
}
