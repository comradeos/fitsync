<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UserController
{
    /** @noinspection PhpUnused */
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function usersList(UserService $service): JsonResponse
    {
        $users = $service->getAll();

        $data = array_map(fn($u) => $u->toArray(), $users);

        return new JsonResponse($data);
    }
}
