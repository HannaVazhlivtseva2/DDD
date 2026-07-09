<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

final readonly class PhoneNumber
{
    private const string PATTERN = '/^\+?[0-9\s\-()]{7,20}$/';

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (!preg_match(self::PATTERN, $trimmed)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid phone number.', $value));
        }

        $this->value = $trimmed;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
