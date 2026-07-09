<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByValidResetToken;

final readonly class ResetTokenView
{
    public function __construct(
        public bool $valid,
        public ?string $email = null,
    ) {
    }
}
