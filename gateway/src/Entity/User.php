<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
        }
    }

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email {
        get {
            return $this->email;
        }
        set {
            $this->email = $value;
        }
    }

    #[ORM\Column(type: "string", length: 255)]
    private string $name {
        get {
            return $this->name;
        }
        set {
            $this->name = $value;
        }
    }

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdAt {
        get {
            return $this->createdAt;
        }
    }

    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'name'      => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
