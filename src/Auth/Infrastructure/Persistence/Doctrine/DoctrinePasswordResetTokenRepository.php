<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Doctrine;

use App\Auth\Domain\Model\PasswordResetToken;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrinePasswordResetTokenRepository implements PasswordResetTokenRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(PasswordResetToken $token): void
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    public function findValidByTokenHash(string $tokenHash, \DateTimeImmutable $now): ?PasswordResetToken
    {
        $token = $this->entityManager->getRepository(PasswordResetToken::class)->findOneBy(['tokenHash' => $tokenHash]);

        if (null === $token || !$token->isValid($now)) {
            return null;
        }

        return $token;
    }
}
