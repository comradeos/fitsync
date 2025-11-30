<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest
{
    #[Assert\Length(min: 2, max: 255)]
    public ?string $name = null;
}
