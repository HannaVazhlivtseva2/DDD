<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Persistence\Doctrine\Type;

use App\Auth\Domain\Model\PhoneNumber;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class PhoneNumberType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 20;

        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?PhoneNumber
    {
        return null === $value ? null : new PhoneNumber($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return $value instanceof PhoneNumber ? $value->toString() : $value;
    }
}
