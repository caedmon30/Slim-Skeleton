<?php

declare(strict_types=1);

namespace App\Domain\Key;

use App\Domain\DomainException\DomainRecordNotFoundException;

class KeyNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The key you requested does not exist.';
}
