<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class ListRequestAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $requests = $this->requestRepository->findAll();
        $this->logger->info("Requests list was viewed.");

        return $this->respondWithData($requests);
    }
}
