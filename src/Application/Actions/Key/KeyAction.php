<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Actions\Action;
use App\Domain\Key\KeyRepository;
use Psr\Log\LoggerInterface;

abstract class KeyAction extends Action
{
    protected KeyRepository $keyRepository;

    public function __construct(LoggerInterface $logger, KeyRepository $keyRepository)
    {
        parent::__construct($logger);
        $this->keyRepository = $keyRepository;
    }
}
