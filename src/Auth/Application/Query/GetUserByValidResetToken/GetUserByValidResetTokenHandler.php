<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByValidResetToken;

use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Auth\Domain\Service\TokenGenerator;

final readonly class GetUserByValidResetTokenHandler
{
    public function __construct(
        private PasswordResetTokenRepository $tokens,
        private UserRepository $users,
        private TokenGenerator $tokenGenerator,
    ) {
    }

    public function __invoke(GetUserByValidResetTokenQuery $query): ResetTokenView
    {
        $tokenHash = $this->tokenGenerator->hash($query->rawToken);
        $token = $this->tokens->findValidByTokenHash($tokenHash, new \DateTimeImmutable());

        if (null === $token) {
            return new ResetTokenView(valid: false);
        }

        $user = $this->users->findById($token->userId());

        if (null === $user) {
            return new ResetTokenView(valid: false);
        }

        return new ResetTokenView(valid: true, email: $user->email()->toString());
    }
}
