<?php

declare(strict_types=1);

namespace App\Application\Actions\Employee;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateEmployeeAction extends EmployeeAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $data = $this->getFormData();
        $employee = $this->employeeRepository->updateEmployeeOfId($userId, $data);
        $this->logger->info("Employee status of id `{$userId}` was updated.");

        return $this->respondWithData($employee);
    }
}
