<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

interface TokenGenerator
{
    /**
     * Generates a random, URL-safe raw token. The raw value is only ever
     * emailed to the user — callers must store hash() of it, never the raw value.
     */
    public function generate(): string;

    public function hash(string $rawToken): string;
}
