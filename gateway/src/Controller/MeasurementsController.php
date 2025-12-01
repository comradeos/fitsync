<?php

namespace App\Controller;

use App\DTO\Measurement\CreateMeasurementRequest;
use App\Service\MeasurementService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MeasurementsController
{
    #[Route('/measurements', methods: ['GET'])]
    public function list(MeasurementService $service): JsonResponse
    {
        return new JsonResponse(
            array_map(fn($m) => $m->toArray(), $service->getAll())
        );
    }

    #[Route('/measurements/{id}', methods: ['GET'])]
    public function info(int $id, MeasurementService $service): JsonResponse
    {
        $m = $service->get($id);
        if (!$m) return new JsonResponse(['error' => 'Measurement not found'], 404);

        return new JsonResponse($m->toArray());
    }

    #[Route('/measurements', methods: ['POST'])]
    public function create(Request $req, MeasurementService $service): JsonResponse
    {
        $data = $req->toArray();

        $dto = new CreateMeasurementRequest();
        $dto->userId     = $data['user_id'] ?? null;
        $dto->deviceId   = $data['device_id'] ?? null;
        $dto->type       = $data['type'] ?? null;
        $dto->value      = $data['value'] ?? null;
        $dto->unit       = $data['unit'] ?? null;

        $error = $service->validateCreate($dto);
        if ($error) return new JsonResponse(['error' => $error], 400);

        $measurement = $service->create($dto);

        return new JsonResponse($measurement->toArray(), 201);
    }

    #[Route('/users/{id}/measurements', methods: ['GET'])]
    public function byUser(int $id, MeasurementService $service): JsonResponse
    {
        $items = $service->getByUser($id);
        return new JsonResponse(array_map(fn($m) => $m->toArray(), $items));
    }

    #[Route('/devices/{id}/measurements', methods: ['GET'])]
    public function byDevice(int $id, MeasurementService $service): JsonResponse
    {
        $items = $service->getByDevice($id);
        return new JsonResponse(array_map(fn($m) => $m->toArray(), $items));
    }
}
