<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

use Symfony\Component\Uid\Uuid;

final readonly class UserId
{
    private function __construct(private Uuid $uuid)
    {
    }

    public static function generate(): self
    {
        return new self(Uuid::v7());
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    public static function fromBinary(string $binary): self
    {
        return new self(Uuid::fromBinary($binary));
    }

    public function toString(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function toBinary(): string
    {
        return $this->uuid->toBinary();
    }

    public function equals(self $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
