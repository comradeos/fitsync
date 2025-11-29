<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Device;

#[ORM\Entity]
#[ORM\Table(name: "measurements")]
class Measurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Device::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Device $device;

    #[ORM\Column(type: "string", length: 50)]
    private string $type;

    #[ORM\Column(type: "float")]
    private float $value;

    #[ORM\Column(type: "string", length: 20)]
    private string $unit;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $measuredAt;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, ?Device $device, string $type, float $value, string $unit, \DateTimeImmutable $measuredAt)
    {
        $this->user = $user;
        $this->device = $device;
        $this->type = $type;
        $this->value = $value;
        $this->unit = $unit;
        $this->measuredAt = $measuredAt;
        $this->createdAt = new \DateTimeImmutable();
    }
}
