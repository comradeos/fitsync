<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController
{
    /** @noinspection PhpUnused */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'service' => 'gateway',
            'php' => PHP_VERSION,
            'time' => gmdate('c'),
        ]);
    }
}
