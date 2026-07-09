<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByValidResetToken;

final readonly class GetUserByValidResetTokenQuery
{
    public function __construct(
        public string $rawToken,
    ) {
    }
}
