<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\HashedPassword;
use App\Auth\Domain\Model\PlainPassword;

interface PasswordHasher
{
    public function hash(PlainPassword $plainPassword): HashedPassword;

    public function verify(HashedPassword $hashedPassword, string $plainPassword): bool;
}
