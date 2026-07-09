<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Doctrine\Type;

use App\Auth\Domain\Model\UserId;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class UserIdType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL(['length' => 16, 'fixed' => true]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?UserId
    {
        return null === $value ? null : UserId::fromBinary($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value instanceof UserId ? $value->toBinary() : $value;
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::BINARY;
    }
}
