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
        private EntityManagerInterface $em,
        private RabbitMQPublisher $publisher
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
        return $this->repository->findBy([], ['createdAt' => 'DESC']);
    }

    public function getByUser(int $userId): array
    {
        return $this->repository->findBy(['user' => $userId], ['createdAt' => 'DESC']);
    }

    public function getByDevice(int $deviceId): array
    {
        return $this->repository->findBy(['device' => $deviceId], ['createdAt' => 'DESC']);
    }

    public function validateCreate(CreateMeasurementRequest $dto): ?string
    {
        if (!$dto->userId) {
            return 'user_id is required';
        }

        if (!$this->userService->get($dto->userId)) {
            return 'User not found';
        }

        if ($dto->deviceId !== null) {
            if (!is_numeric($dto->deviceId)) {
                return 'device_id must be numeric';
            }
            if (!$this->deviceService->get($dto->deviceId)) {
                return 'Device not found';
            }
        }

        if (!$dto->type) {
            return 'type is required';
        }
        if (!is_string($dto->type)) {
            return 'type must be a string';
        }
        if (strlen($dto->type) > 50) {
            return 'type is too long';
        }

        if ($dto->value === null) {
            return 'value is required';
        }
        if (!is_numeric($dto->value)) {
            return 'value must be numeric';
        }

        if (!$dto->unit) {
            return 'unit is required';
        }
        if (!is_string($dto->unit)) {
            return 'unit must be a string';
        }
        if (strlen($dto->unit) > 20) {
            return 'unit is too long';
        }

        return null;
    }

    public function create(CreateMeasurementRequest $dto): Measurement
    {
        $user   = $this->userService->get($dto->userId);
        $device = $dto->deviceId ? $this->deviceService->get($dto->deviceId) : null;

        $measurement = new Measurement(
            user:   $user,
            device: $device,
            type:   $dto->type,
            value:  (float)$dto->value,
            unit:   $dto->unit
        );

        $this->em->persist($measurement);
        $this->em->flush();

        $this->publishCreatedEvent($measurement);

        return $measurement;
    }

    private function publishCreatedEvent(Measurement $m): void
    {
        $this->publisher->publish("measurement.created", [
            'id' => $m->id,
            'user_id' => $m->user->id,
            'device_id' => $m->device?->id,
            'type' => $m->type,
            'value' => $m->value,
            'unit' => $m->unit,
            'created_at' => $m->createdAt->format('c'),
        ]);
    }
}
