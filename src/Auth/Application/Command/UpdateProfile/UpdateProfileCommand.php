<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\UpdateProfile;

final readonly class UpdateProfileCommand
{
    public function __construct(
        public string $userId,
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $gender,
    ) {
    }
}
