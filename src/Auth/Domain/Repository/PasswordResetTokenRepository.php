<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Model\PasswordResetToken;

interface PasswordResetTokenRepository
{
    public function save(PasswordResetToken $token): void;

    public function findValidByTokenHash(string $tokenHash, \DateTimeImmutable $now): ?PasswordResetToken;
}
