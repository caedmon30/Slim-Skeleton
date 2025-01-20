<?php

namespace App\Domain\Employee;

interface EmployeeRepository
{
    /**
     * @return Employee[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Employee
     * @throws EmployeeNotFoundException
     */
    public function findEmployeeOfId(int $id): Employee;

    public function deleteEmployeeOfId(int $id): array;

    public function updateEmployeeOfId(int $id, array $data): array;

    public function createEmployee(array $data): array;
}