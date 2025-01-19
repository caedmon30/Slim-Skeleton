<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->getFormData();
        $user = $this->userRepository->createUser($data);
        $this->logger->info("New user created!");

        return $this->respondWithData($user);
    }
}
