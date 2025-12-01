<?php

namespace App\Service;

use App\DTO\User\CreateUserRequest;
use App\DTO\User\UpdateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserService
{
    public function __construct(
        private UserRepository         $repository,
        private EntityManagerInterface $em
    ) {}

    public function create(CreateUserRequest $dto): User
    {
        $user = new User(
            email: $dto->email,
            name: $dto->name,
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function update(User $user, UpdateUserRequest $dto): User
    {
        if ($dto->name !== null) {
            $user->setName($dto->name);
        }

        $this->em->flush();

        return $user;
    }

    /**
     * @return ?object
     */
    public function get(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['id' => 'ASC']);
    }

    /**
     * @return ?object
     */
    public function getByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function validateCreate(CreateUserRequest $dto): ?string
    {
        if (!$dto->email) {
            return "Email is required";
        }

        if (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format";
        }

        if ($this->getByEmail($dto->email) !== null) {
            return "Email already exists";
        }

        if (!$dto->name) {
            return "Name is required";
        }

        if (strlen($dto->name) < 2) {
            return "Name must be at least 2 characters";
        }

        return null;
    }

    public function validateUpdate(UpdateUserRequest $dto): ?string
    {
        if ($dto->name == null) {
            return "Name cannot be empty";
        }

        if (strlen($dto->name) < 2) {
            return "Name must be at least 2 characters";
        }

        return null;
    }
}
