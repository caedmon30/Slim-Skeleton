<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Application\Actions\Action;
use App\Domain\Request\RequestRepository;
use Psr\Log\LoggerInterface;

abstract class RequestAction extends Action
{
    protected RequestRepository $requestRepository;

    public function __construct(LoggerInterface $logger, RequestRepository $requestRepository)
    {
        parent::__construct($logger);
        $this->requestRepository = $requestRepository;
    }
}
