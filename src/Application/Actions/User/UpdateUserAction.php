<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $data = $this->getFormData();
        $this->userRepository->updateUserOfId($userId, $data);
        $user = $this->userRepository->findAll();
        $this->logger->info("User of id `{$userId}` was updated.");

        return $this->respondWithData($user);
    }
}
