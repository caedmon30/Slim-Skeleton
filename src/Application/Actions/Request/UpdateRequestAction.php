<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateRequestAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $data = $this->getFormData();
        $request = (array)$this->requestRepository->updateRequestOfId($userId, $data);
        $this->logger->info("Request id `{$userId}` was updated.");

        return $this->respondWithData($request);
    }
}
