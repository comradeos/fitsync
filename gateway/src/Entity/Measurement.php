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
    public ?int $id = null {
        get {
            return $this->id;
        }
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public User $user {
        get {
            return $this->user;
        }
    }

    #[ORM\ManyToOne(targetEntity: Device::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Device $device {
        get {
            return $this->device;
        }
    }

    #[ORM\Column(type: "string", length: 50)]
    public string $type {
        get {
            return $this->type;
        }
    }

    #[ORM\Column(type: "float")]
    public float $value {
        get {
            return $this->value;
        }
    }

    #[ORM\Column(type: "string", length: 20)]
    public string $unit {
        get {
            return $this->unit;
        }
    }

    #[ORM\Column(type: "datetime_immutable")]
    public DateTimeImmutable $createdAt {
        get {
            return $this->createdAt;
        }
    }

    public function __construct(
        User $user,
        ?Device $device,
        string $type,
        float $value,
        string $unit,
    ) {
        $this->user      = $user;
        $this->device    = $device;
        $this->type      = $type;
        $this->value     = $value;
        $this->unit      = $unit;
        $this->createdAt = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'device_id' => $this->device?->id,
            'type' => $this->type,
            'value' => $this->value,
            'unit' => $this->unit,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

}
