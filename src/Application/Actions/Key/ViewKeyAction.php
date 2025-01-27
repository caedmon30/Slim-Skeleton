<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;

class ViewKeyAction extends KeyAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $keyId = (int) $this->resolveArg('id');
        $key = $this->keyRepository->findKeyOfId($keyId);
        $this->logger->info("Request of id `{$keyId}` was viewed.");

        return $this->respondWithData($key);
    }
}
