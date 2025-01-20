<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use Psr\Http\Message\ResponseInterface as Response;

class CreateEmployeeAction extends EmployeeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $employee = $this->employeeRepository->createEmployee($data);
        $this->logger->info("New employee status created!");

        return $this->respondWithData($employee);
    }
}
