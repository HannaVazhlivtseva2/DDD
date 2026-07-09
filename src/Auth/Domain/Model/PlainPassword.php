<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

use App\Auth\Domain\Exception\WeakPassword;

/**
 * A plain-text password that has passed the domain's complexity policy.
 * Holds the raw value only transiently, until it's hashed — never persisted.
 */
final readonly class PlainPassword
{
    private const int MIN_LENGTH = 8;

    public function __construct(private string $value)
    {
        if (
            \strlen($value) < self::MIN_LENGTH
            || !preg_match('/[a-z]/', $value)
            || !preg_match('/[A-Z]/', $value)
            || !preg_match('/\d/', $value)
            || !preg_match('/[^a-zA-Z0-9]/', $value)
        ) {
            throw WeakPassword::create();
        }
    }

    public function toString(): string
    {
        return $this->value;
    }
}
