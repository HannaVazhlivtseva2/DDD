<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

interface TokenGenerator
{

    public function generate(): string;

    public function hash(string $rawToken): string;
}
