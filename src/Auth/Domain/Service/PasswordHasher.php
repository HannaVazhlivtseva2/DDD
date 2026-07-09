<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\HashedPassword;

interface PasswordHasher
{
    public function hash(string $plainPassword): HashedPassword;

    public function verify(HashedPassword $hashedPassword, string $plainPassword): bool;
}
