<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use Psr\Http\Message\ResponseInterface as Response;

class ListEmployeesAction extends EmployeeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $employees = $this->employeeRepository->findAll();
        $this->logger->info("Employee status list was viewed.");

        return $this->respondWithData($employees);
    }
}
