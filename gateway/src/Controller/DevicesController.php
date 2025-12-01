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
    #[Route('/devices', name: 'devices_list', methods: ['GET'])]
    public function list(DeviceService $service): JsonResponse
    {
        $devices = $service->getAll();
        $data = array_map(fn($d) => $d->toArray(), $devices);
        return new JsonResponse($data);
    }

    #[Route('/devices', name: 'devices_create', methods: ['POST'])]
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
            return new JsonResponse(['error' => $error]);
        }

        $device = $service->create($dto);

        return new JsonResponse($device->toArray());
    }

    #[Route('/devices/{id}', name: 'devices_update', methods: ['PUT'])]
    public function update(int $id, Request $request, DeviceService $service): JsonResponse
    {
        $device = $service->get($id);

        if (!$device) {
            return new JsonResponse(['error' => 'Device not found']);
        }

        $data = $request->toArray();

        $dto = new UpdateDeviceRequest();
        $dto->type  = $data['type'] ?? null;
        $dto->model = $data['model'] ?? null;

        $error = $service->validateUpdate($dto);
        if ($error !== null) {
            return new JsonResponse(['error' => $error]);
        }

        $updated = $service->update($device, $dto);

        return new JsonResponse($updated->toArray());
    }

    #[Route('/devices/{id}', name: 'devices_info', methods: ['GET'])]
    public function info(int $id, DeviceService $service): JsonResponse
    {
        $device = $service->get($id);

        if (!$device) {
            return new JsonResponse(['error' => 'Device not found']);
        }

        return new JsonResponse($device->toArray());
    }
}
