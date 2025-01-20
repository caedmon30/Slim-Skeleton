<?php

declare(strict_types=1);

namespace App\Domain\Employee;

use App\Domain\DomainException\DomainRecordNotFoundException;

class EmployeeNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The employee type you requested does not exist.';
}
