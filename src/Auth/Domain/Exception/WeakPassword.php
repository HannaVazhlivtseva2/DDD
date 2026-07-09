<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

final class WeakPassword extends \DomainException
{
    public static function create(): self
    {
        return new self('Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a digit, and a symbol.');
    }
}
