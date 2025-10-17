<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class UserDto extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    )
    {
    }
}
