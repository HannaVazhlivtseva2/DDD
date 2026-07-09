<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

final class InvalidOrExpiredResetToken extends \DomainException
{
    public static function create(): self
    {
        return new self('This password reset link is invalid or has expired.');
    }
}
