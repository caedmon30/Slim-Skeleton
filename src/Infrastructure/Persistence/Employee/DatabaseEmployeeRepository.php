<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Employee;

use App\Domain\Employee\Employee;
use App\Domain\Employee\EmployeeNotFoundException;
use App\Domain\Employee\EmployeeRepository;
use Nette\Database\Connection;

class DatabaseEmployeeRepository implements EmployeeRepository
{
    private array $employees;
    private Connection $connection;

    public function __construct(Connection $connection, array|null $employees = null)
    {

        $this->connection = $connection;

        $results = $this->connection->query("SELECT * FROM tblempstatus");

        foreach ($results as $row) {
            $employees[(int)$row['id']] = new Employee(
                (int)$row->id,
                $row->empStatusName
            );
        }
        $this->employees = $employees;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {

        return array_values($this->employees);
    }

    /**
     * {@inheritdoc}
     */
    public function findEmployeeOfId(int $id): Employee
    {
        if (!isset($this->employees[$id])) {
            throw new EmployeeNotFoundException();
        }

        return $this->employees[$id];
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function deleteEmployeeOfId(int $id): array
    {
        if (!isset($this->employees[$id])) {
            throw new EmployeeNotFoundException();
        }

        $this->connection->query('DELETE FROM tblempstatus WHERE id = ?', $id);
        return array_values($this->employees);
    }


    /**
     * @throws EmployeeNotFoundException
     */
    public function updateEmployeeOfId(int $id, array $data): array
    {

        if (!isset($this->employees[$id])) {
            throw new EmployeeNotFoundException();
        }
        $this->connection->query('UPDATE tblempstatus SET ? WHERE id = ?', $data, $id);
        return array_values($this->employees);
    }

    public function createEmployee(array $data): array
    {
        $this->connection->query('INSERT INTO tblempstatus ?', $data);
        return array_values($this->employees);
    }
}
