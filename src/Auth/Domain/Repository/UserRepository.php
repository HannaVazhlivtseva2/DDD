<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Model\Email;
use App\Auth\Domain\Model\User;
use App\Auth\Domain\Model\UserId;

interface UserRepository
{
    public function save(User $user): void;

    public function findByEmail(Email $email): ?User;

    public function findById(UserId $id): ?User;

    public function existsWithEmail(Email $email): bool;
}
