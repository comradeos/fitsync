<?php

namespace App\Service;

use App\DTO\Measurement\CreateMeasurementRequest;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeasurementRepository;

readonly class MeasurementService
{
    public function __construct(
        private MeasurementRepository $repository,
        private UserService $userService,
        private DeviceService $deviceService,
        private EntityManagerInterface $em
    ) {}

    /**
     * @return ?object
     */
    public function get(int $id): ?Measurement
    {
        return $this->repository->find($id);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['id' => 'DESC']);
    }

    public function getByUser(int $userId): array
    {
        return $this->repository->findBy(['user' => $userId], ['id' => 'DESC']);
    }

    public function getByDevice(int $deviceId): array
    {
        return $this->repository->findBy(['device' => $deviceId], ['id' => 'DESC']);
    }

    public function validateCreate(CreateMeasurementRequest $dto): ?string
    {
        if (!$dto->userId) {
            return "user_id is required";
        }

        $user = $this->userService->get($dto->userId);
        if (!$user) {
            return "User not found";
        }

        if ($dto->deviceId !== null) {
            $device = $this->deviceService->get($dto->deviceId);

            if (!$device) {
                return "Device not found";
            }
        }

        if (!$dto->type) {
            return "Type is required";
        }

        if (strlen($dto->type) < 2) {
            return "type must be at least 2 characters";
        }

        if ($dto->value === null) {
            return "value is required";
        }

        if (!is_numeric($dto->value)) {
            return "value must be a number";
        }

        if (!$dto->unit) {
            return "unit is required";
        }

        return null;
    }

    public function create(CreateMeasurementRequest $dto): Measurement
    {
        $user   = $this->userService->get($dto->userId);
        $device = $dto->deviceId ? $this->deviceService->get($dto->deviceId) : null;

        $measurement = new Measurement(
            user: $user,
            device: $device,
            type: $dto->type,
            value: $dto->value,
            unit: $dto->unit
        );

        $this->em->persist($measurement);
        $this->em->flush();

        return $measurement;
    }
}
