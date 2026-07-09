<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Service\TokenGenerator;

final class TokenGeneratorAdapter implements TokenGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function hash(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }
}
