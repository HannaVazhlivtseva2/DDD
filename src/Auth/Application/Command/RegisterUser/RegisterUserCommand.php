<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUser;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $plainPassword,
        public string $phone,
        public string $gender,
    ) {
    }
}
