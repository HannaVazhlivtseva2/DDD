<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\UserId;

interface AvatarStorage
{
    /**
     * Stores the given raw file content as the user's avatar, replacing any
     * previous one, and returns the stored filename.
     */
    public function store(UserId $userId, string $content, string $extension): string;

    public function delete(string $filename): void;
}
