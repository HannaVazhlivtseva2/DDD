<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

final readonly class HashedPassword
{
    public function __construct(private string $hash)
    {
        if ('' === $hash) {
            throw new \InvalidArgumentException('A hashed password cannot be empty.');
        }
    }

    public function toString(): string
    {
        return $this->hash;
    }

    public function __toString(): string
    {
        return $this->hash;
    }
}
