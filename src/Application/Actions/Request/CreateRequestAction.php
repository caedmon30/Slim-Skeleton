<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class CreateRequestAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $request = $this->requestRepository->createRequest($data);
        $this->logger->info("New request created!");

        return $this->respondWithData($request);
    }
}
