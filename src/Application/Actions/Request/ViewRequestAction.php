<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class ViewRequestAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $requestId = (int) $this->resolveArg('id');
        $request = $this->requestRepository->findRequestOfId($requestId);
        $this->logger->info("Request of id `{$requestId}` was viewed.");

        return $this->respondWithData($request);
    }
}
