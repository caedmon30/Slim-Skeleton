<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;

class CreateKeyAction extends KeyAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $key = $this->keyRepository->createKey($data);
        $this->logger->info("New key was created!");

        return $this->respondWithData($key);
    }
}
