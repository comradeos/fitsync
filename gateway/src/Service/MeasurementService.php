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
        if (empty($dto->userId)) {
            return 'user_id is required';
        }

        if (empty($dto->deviceId)) {
            return 'device_id is required';
        }

        if (empty($dto->type)) {
            return 'type is required';
        }

        if (!is_string($dto->type)) {
            return 'type must be a string';
        }

        if ($dto->value === null) {
            return 'value is required';
        }

        if (!is_numeric($dto->value)) {
            return 'value must be a number';
        }

        if (empty($dto->unit)) {
            return 'unit is required';
        }

        if (!is_string($dto->unit)) {
            return 'unit must be a string';
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
