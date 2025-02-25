<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;

class ListKeysAction extends KeyAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $keys = $this->keyRepository->findAll();

        $this->logger->info("Keys list was viewed.");

        return $this->respondWithData($keys);
    }
}
