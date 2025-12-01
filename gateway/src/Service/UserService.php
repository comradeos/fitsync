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
     * @return object|null
     */
    public function get(int $id): User | null
    {
        $user = $this->repository->find($id);

        if (is_null($user)) {
            return null;
        }

        return $user;
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['id' => 'ASC']);
    }
}
