<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateKeyAction extends KeyAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $keyId = (int) $this->resolveArg('id');
        $data = $this->getFormData();
        $key = $this->keyRepository->updateKeyOfId($keyId, $data);
        $this->logger->info("Key with id `{$keyId}` was updated.");

        return $this->respondWithData($key);
    }
}
