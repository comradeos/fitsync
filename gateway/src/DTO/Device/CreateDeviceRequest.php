<?php

namespace App\DTO\Device;

class CreateDeviceRequest
{
    public ?int $userId = null;
    public ?string $type = null;
    public ?string $model = null;
    public ?string $externalId = null;
}
