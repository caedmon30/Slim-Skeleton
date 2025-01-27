<?php

declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\DomainException\DomainRecordNotFoundException;

class RequestNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The request you selected does not exist.';
}
