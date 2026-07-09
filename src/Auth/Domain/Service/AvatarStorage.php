<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\UserId;

interface AvatarStorage
{
    public function store(UserId $userId, string $content, string $extension): string;

    public function delete(string $filename): void;

    public function publicUrl(string $filename): string;

    public function exists(string $filename): bool;
}
