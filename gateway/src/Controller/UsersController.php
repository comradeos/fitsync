<?php

namespace App\Controller;

use App\DTO\User\CreateUserRequest;
use App\DTO\User\UpdateUserRequest;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UsersController
{
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function list(UserService $service): JsonResponse
    {
        $users = $service->getAll();
        $data = array_map(fn($u) => $u->toArray(), $users);
        return new JsonResponse($data);
    }

    #[Route('/users', name: 'users_create', methods: ['POST'])]
    public function create(Request $request, UserService $service): JsonResponse
    {
        $data = $request->toArray();

        $dto = new CreateUserRequest();
        $dto->email = $data['email'] ?? null;
        $dto->name  = $data['name'] ?? null;

        $error = $service->validateCreate($dto);
        if ($error !== null) {
            return new JsonResponse(['error' => $error]);
        }

        $user = $service->create($dto);

        return new JsonResponse($user->toArray());
    }

    #[Route('/users/{id}', name: 'users_update', methods: ['PUT'])]
    public function update(int $id, Request $request, UserService $service): JsonResponse
    {
        $user = $service->get($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found']);
        }

        $data = $request->toArray();

        $dto = new UpdateUserRequest();
        $dto->name = $data['name'] ?? null;

        $error = $service->validateUpdate($dto);
        if ($error !== null) {
            return new JsonResponse(['error' => $error]);
        }

        $updated = $service->update($user, $dto);

        return new JsonResponse($updated->toArray());
    }

    #[Route('/users/{id}', name: 'users_info', methods: ['GET'])]
    public function info(int $id, UserService $service): JsonResponse
    {
        $user = $service->get($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse($user->toArray());
    }
}
