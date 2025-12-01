<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "measurements")]
class Measurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null {
        get => $this->id;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user {
        get => $this->user;
    }

    #[ORM\ManyToOne(targetEntity: Device::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Device $device {
        get => $this->device;
    }

    #[ORM\Column(type: "string", length: 50)]
    private string $type {
        get => $this->type;
    }

    #[ORM\Column(type: "float")]
    private float $value {
        get => $this->value;
    }

    #[ORM\Column(type: "string", length: 20)]
    private string $unit {
        get => $this->unit;
    }

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    public function __construct(
        User $user,
        ?Device $device,
        string $type,
        float $value,
        string $unit,
    ) {
        $this->user       = $user;
        $this->device     = $device;
        $this->type       = $type;
        $this->value      = $value;
        $this->unit       = $unit;
        $this->createdAt  = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user->id,
            'device_id'  => $this->device?->id,
            'type'       => $this->type,
            'value'      => $this->value,
            'unit'       => $this->unit,
            'created_at'  => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
