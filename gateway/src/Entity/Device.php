<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "devices")]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public ?int $id = null {
        get => $this->id;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user {
        get => $this->user;
    }

    #[ORM\Column(type: "string", length: 50)]
    public string $type {
        get => $this->type;
        set(string $value) => $this->type = $value;
    }

    #[ORM\Column(type: "string", length: 100)]
    public string $model {
        get => $this->model;
        set(string $value) => $this->model = $value;
    }

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $externalId {
        get => $this->externalId;
    }

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    public function __construct(
        User $user,
        string $type,
        string $model,
        string $externalId
    ) {
        $this->user       = $user;
        $this->type       = $type;
        $this->model      = $model;
        $this->externalId = $externalId;
        $this->createdAt  = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user->id,
            'type'       => $this->type,
            'model'      => $this->model,
            'externalId' => $this->externalId,
            'createdAt'  => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
