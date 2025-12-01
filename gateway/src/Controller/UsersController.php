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
        $dto->name = $data['name'] ?? null;

        if (!$dto->email || !filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email'], 400);
        }

        if (!$dto->name || strlen($dto->name) < 2) {
            return new JsonResponse(['error' => 'Invalid name'], 400);
        }

        $user = $service->create($dto);

        return new JsonResponse($user->toArray(), 201);
    }

    #[Route('/users/{id}', name: 'users_update', methods: ['PUT'])]
    public function update(int $id, Request $request, UserService $service): JsonResponse
    {
        $user = $service->get($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $data = $request->toArray();

        $dto = new UpdateUserRequest();
        $dto->name = $data['name'] ?? null;

        if ($dto->name !== null && strlen($dto->name) < 2) {
            return new JsonResponse(['error' => 'Invalid name'], 400);
        }

        $updated = $service->update($user, $dto);

        return new JsonResponse($updated->toArray());
    }
}
