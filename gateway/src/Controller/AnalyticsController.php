<?php

namespace App\Controller;

use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AnalyticsController
{
    /** @noinspection PhpUnused */
    #[Route('/analytics/users/{id}/{type}', methods: ['GET'])]
    public function userStats(int $id, string $type): JsonResponse
    {
        $redis = new Client([
            'host' => 'redis',
            'port' => 6379
        ]);

        $key = "stats:user:$id:type:$type";
        $value = $redis->get($key) ?? 0;

        return new JsonResponse([
            'user_id' => $id,
            'type' => $type,
            'total' => (float)$value
        ]);
    }
}
