<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RequestPasswordReset;

use App\Auth\Application\Port\EventPublisher;
use App\Auth\Domain\Event\PasswordResetRequested;
use App\Auth\Domain\Model\Email;
use App\Auth\Domain\Model\PasswordResetToken;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Auth\Domain\Service\TokenGenerator;

final readonly class RequestPasswordResetHandler
{
    private const string TOKEN_TTL = 'PT1H';

    public function __construct(
        private UserRepository $users,
        private PasswordResetTokenRepository $tokens,
        private TokenGenerator $tokenGenerator,
        private EventPublisher $events,
    ) {
    }

    public function __invoke(RequestPasswordResetCommand $command): void
    {
        $email = new Email($command->email);
        $user = $this->users->findByEmail($email);

        if (null === $user) {
            return;
        }

        $rawToken = $this->tokenGenerator->generate();
        $expiresAt = (new \DateTimeImmutable())->add(new \DateInterval(self::TOKEN_TTL));

        $token = PasswordResetToken::issue($user->id(), $this->tokenGenerator->hash($rawToken), $expiresAt);
        $this->tokens->save($token);

        $this->events->publish([
            new PasswordResetRequested($user->id()->toString(), $email->toString(), $rawToken, $expiresAt),
        ]);
    }
}
