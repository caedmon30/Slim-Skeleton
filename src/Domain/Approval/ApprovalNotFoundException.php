<?php

declare(strict_types=1);

namespace App\Domain\Approval;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ApprovalNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The approval you selected does not exist.';
}
