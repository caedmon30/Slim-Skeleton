<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use App\Application\Actions\Action;
use App\Domain\Employee\EmployeeRepository;
use Psr\Log\LoggerInterface;

abstract class EmployeeAction extends Action
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(LoggerInterface $logger, EmployeeRepository $employeeRepository)
    {
        parent::__construct($logger);
        $this->employeeRepository = $employeeRepository;
    }
}
