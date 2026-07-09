<?php

declare(strict_types=1);

namespace App\Auth\Domain\Event;

final readonly class UserLoggedIn implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $email,
    ) {
    }
}
