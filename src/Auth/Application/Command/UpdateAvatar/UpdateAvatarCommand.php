<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\UpdateAvatar;

final readonly class UpdateAvatarCommand
{
    public function __construct(
        public string $userId,
        public string $filename,
    ) {
    }
}
