<?php

namespace App\DTO\Measurement;

class CreateMeasurementRequest
{
    public ?int $userId = null;
    public ?int $deviceId = null;
    public ?string $type = null;
    public ?float $value = null;
    public ?string $unit = null;
}
