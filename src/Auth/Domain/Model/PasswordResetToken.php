<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'password_reset_tokens')]
final class PasswordResetToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private readonly string $id;

    #[ORM\Column(type: 'user_id')]
    private readonly UserId $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private readonly string $tokenHash;

    #[ORM\Column(type: 'datetime_immutable')]
    private readonly \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $usedAt = null;

    private function __construct(UserId $userId, string $tokenHash, \DateTimeImmutable $expiresAt)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->userId = $userId;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
    }

    public static function issue(UserId $userId, string $tokenHash, \DateTimeImmutable $expiresAt): self
    {
        return new self($userId, $tokenHash, $expiresAt);
    }

    public function isValid(\DateTimeImmutable $now): bool
    {
        return null === $this->usedAt && $now < $this->expiresAt;
    }

    public function markUsed(): void
    {
        $this->usedAt = new \DateTimeImmutable();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function tokenHash(): string
    {
        return $this->tokenHash;
    }
}
