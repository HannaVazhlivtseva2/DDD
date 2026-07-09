<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\ResetPassword;

final readonly class ResetPasswordCommand
{
    public function __construct(
        public string $rawToken,
        public string $newPlainPassword,
    ) {
    }
}
