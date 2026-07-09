<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Model\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private User $domainUser)
    {
    }

    public function domainUser(): User
    {
        return $this->domainUser;
    }

    public function getRoles(): array
    {
        return $this->domainUser->roles();
    }

    public function getPassword(): string
    {
        return $this->domainUser->password()->toString();
    }

    public function getUserIdentifier(): string
    {
        return $this->domainUser->email()->toString();
    }

    public function eraseCredentials(): void
    {
    }
}
