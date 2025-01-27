<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteRequestAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $requestId = (int) $this->resolveArg('id');
        $request = $this->requestRepository->deleteRequestOfId($requestId);
        $this->logger->info("Request of id `{$requestId}` was deleted.");

        return $this->respondWithData($request);
    }
}
