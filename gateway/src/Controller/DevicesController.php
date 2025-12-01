<?php

namespace App\Controller;

use App\DTO\Device\CreateDeviceRequest;
use App\DTO\Device\UpdateDeviceRequest;
use App\Service\DeviceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DevicesController
{
    #[Route('/devices', methods: ['GET'])]
    public function list(DeviceService $service): JsonResponse
    {
        $devices = $service->getAll();
        return new JsonResponse(array_map(fn($d) => $d->toArray(), $devices));
    }

    #[Route('/devices', methods: ['POST'])]
    public function create(Request $request, DeviceService $service): JsonResponse
    {
        $data = $request->toArray();

        $dto = new CreateDeviceRequest();
        $dto->userId     = $data['user_id'] ?? null;
        $dto->type       = $data['type'] ?? null;
        $dto->model      = $data['model'] ?? null;
        $dto->externalId = $data['external_id'] ?? null;

        $error = $service->validateCreate($dto);
        if ($error !== null) {
            return new JsonResponse(['error' => $error], 400);
        }

        $device = $service->create($dto);
        return new JsonResponse($device->toArray(), 201);
    }

    #[Route('/devices/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, DeviceService $service): JsonResponse
    {
        $device = $service->get($id);
        if (!$device) {
            return new JsonResponse(['error' => 'Device not found'], 404);
        }

        $data = $request->toArray();
        $dto = new UpdateDeviceRequest();
        $dto->type  = $data['type'] ?? null;
        $dto->model = $data['model'] ?? null;

        $error = $service->validateUpdate($dto);
        if ($error !== null) {
            return new JsonResponse(['error' => $error], 400);
        }

        $updated = $service->update($device, $dto);
        return new JsonResponse($updated->toArray());
    }

    #[Route('/devices/{id}', methods: ['GET'])]
    public function info(int $id, DeviceService $service): JsonResponse
    {
        $device = $service->get($id);

        if (!$device) {
            return new JsonResponse(['error' => 'Device not found'], 404);
        }

        return new JsonResponse($device->toArray());
    }
}
