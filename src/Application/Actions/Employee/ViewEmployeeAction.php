<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use Psr\Http\Message\ResponseInterface as Response;

class ViewEmployeeAction extends EmployeeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $employeeId = (int) $this->resolveArg('id');
        $employee = $this->employeeRepository->findEmployeeOfId($employeeId);
        $this->logger->info("Employee status of id `{$employeeId}` was viewed.");

        return $this->respondWithData($employee);
    }
}
