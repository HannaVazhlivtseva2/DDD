<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

final class EmailAlreadyExists extends \DomainException
{
    public static function withEmail(string $email): self
    {
        return new self(\sprintf('A user with email "%s" already exists.', $email));
    }
}
