<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\ResetPassword;

use App\Auth\Application\Port\EventPublisher;
use App\Auth\Domain\Exception\InvalidOrExpiredResetToken;
use App\Auth\Domain\Model\PlainPassword;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Auth\Domain\Service\PasswordHasher;
use App\Auth\Domain\Service\TokenGenerator;

final readonly class ResetPasswordHandler
{
    public function __construct(
        private UserRepository $users,
        private PasswordResetTokenRepository $tokens,
        private TokenGenerator $tokenGenerator,
        private PasswordHasher $passwordHasher,
        private EventPublisher $events,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): void
    {
        $tokenHash = $this->tokenGenerator->hash($command->rawToken);
        $token = $this->tokens->findValidByTokenHash($tokenHash, new \DateTimeImmutable());

        if (null === $token) {
            throw InvalidOrExpiredResetToken::create();
        }

        $user = $this->users->findById($token->userId());
        if (null === $user) {
            throw InvalidOrExpiredResetToken::create();
        }

        $user->changePassword($this->passwordHasher->hash(new PlainPassword($command->newPlainPassword)));
        $token->markUsed();

        $this->users->save($user);
        $this->tokens->save($token);
        $this->events->publish($user->pullDomainEvents());
    }
}
