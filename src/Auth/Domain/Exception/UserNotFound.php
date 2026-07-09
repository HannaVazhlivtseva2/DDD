<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

final class UserNotFound extends \DomainException
{
    public static function withEmail(string $email): self
    {
        return new self(\sprintf('No user found with email "%s".', $email));
    }

    public static function withId(string $id): self
    {
        return new self(\sprintf('No user found with id "%s".', $id));
    }
}
