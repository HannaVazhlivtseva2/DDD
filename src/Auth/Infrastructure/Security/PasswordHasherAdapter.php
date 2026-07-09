<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Model\HashedPassword;
use App\Auth\Domain\Service\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final readonly class PasswordHasherAdapter implements PasswordHasher
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory)
    {
    }

    public function hash(string $plainPassword): HashedPassword
    {
        return new HashedPassword(
            $this->passwordHasherFactory->getPasswordHasher(SecurityUser::class)->hash($plainPassword),
        );
    }

    public function verify(HashedPassword $hashedPassword, string $plainPassword): bool
    {
        return $this->passwordHasherFactory
            ->getPasswordHasher(SecurityUser::class)
            ->verify($hashedPassword->toString(), $plainPassword);
    }
}
