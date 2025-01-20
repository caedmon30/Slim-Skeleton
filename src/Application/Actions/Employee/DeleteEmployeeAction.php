<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteEmployeeAction extends EmployeeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $employeeId = (int) $this->resolveArg('id');
        $employee = $this->employeeRepository->deleteEmployeeOfId($employeeId);
        $this->logger->info("Employee status of id `{$employeeId}` was deleted.");

        return $this->respondWithData($employee);
    }
}
