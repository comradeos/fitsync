<?php

namespace App\Service;

use App\DTO\Device\CreateDeviceRequest;
use App\DTO\Device\UpdateDeviceRequest;
use App\Entity\Device;
use App\Repository\DeviceRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeviceService
{
    public function __construct(
        private DeviceRepository $repository,
        private UserService $userService,
        private EntityManagerInterface $em
    ) {}

    /**
     * @return ?object
     */
    public function get(int $id): ?Device
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
    public function getByExternalId(string $externalId): ?Device
    {
        return $this->repository->findOneBy(['externalId' => $externalId]);
    }

    public function validateCreate(CreateDeviceRequest $dto): ?string
    {
        if (!$dto->userId) {
            return "User ID is required";
        }

        $user = $this->userService->get($dto->userId);
        if (!$user) {
            return "User not found";
        }

        if (!$dto->type) {
            return "Type is required";
        }

        if (strlen($dto->type) < 2) {
            return "Type must be at least 2 characters";
        }

        if (!$dto->model) {
            return "Model is required";
        }

        if (strlen($dto->model) < 2) {
            return "Model must be at least 2 characters";
        }

        if (!$dto->externalId) {
            return "externalId is required";
        }

        if ($this->getByExternalId($dto->externalId)) {
            return "external_id already exists";
        }

        if (strlen($dto->type) > 50) {
            return "Type cannot exceed 50 characters";
        }

        if (strlen($dto->model) > 100) {
            return "Model cannot exceed 100 characters";
        }

        return null;
    }

    public function validateUpdate(UpdateDeviceRequest $dto): ?string
    {
        if ($dto->type !== null) {
            if ($dto->type === "") {
                return "Type cannot be empty";
            }
            if (strlen($dto->type) < 2) {
                return "Type must be at least 2 characters";
            }
        }

        if ($dto->model !== null) {
            if ($dto->model === "") {
                return "Model cannot be empty";
            }
            if (strlen($dto->model) < 2) {
                return "Model must be at least 2 characters";
            }
        }

        return null;
    }

    public function create(CreateDeviceRequest $dto): Device
    {
        $user = $this->userService->get($dto->userId);

        $device = new Device(
            user: $user,
            type: $dto->type,
            model: $dto->model,
            externalId: $dto->externalId
        );

        $this->em->persist($device);
        $this->em->flush();

        return $device;
    }

    public function update(Device $device, UpdateDeviceRequest $dto): Device
    {
        if ($dto->type !== null) {
            $device->type = $dto->type;
        }

        if ($dto->model !== null) {
            $device->model = $dto->model;
        }

        $this->em->flush();

        return $device;
    }
}
