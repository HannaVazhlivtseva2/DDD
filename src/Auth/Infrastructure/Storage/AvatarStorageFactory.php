<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Storage;

use App\Auth\Domain\Service\AvatarStorage;

final class AvatarStorageFactory
{
    public static function create(
        string $driver,
        LocalFilesystemAvatarStorage $local,
        S3AvatarStorage $s3,
    ): AvatarStorage {
        return match ($driver) {
            's3' => $s3,
            default => $local,
        };
    }
}
