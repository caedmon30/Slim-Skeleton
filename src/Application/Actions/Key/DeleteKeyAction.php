<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteKeyAction extends KeyAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $keyId = (int) $this->resolveArg('id');
        $key= $this->keyRepository->deleteKeyOfId($keyId);
        $this->logger->info("Key of id `{$keyId}` was deleted.");

        return $this->respondWithData($key);
    }
}
