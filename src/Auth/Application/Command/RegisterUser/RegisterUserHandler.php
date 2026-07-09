<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUser;

use App\Auth\Application\Port\EventPublisher;
use App\Auth\Domain\Exception\EmailAlreadyExists;
use App\Auth\Domain\Model\Email;
use App\Auth\Domain\Model\User;
use App\Auth\Domain\Model\UserId;
use App\Auth\Domain\Repository\UserRepository;
use App\Auth\Domain\Service\PasswordHasher;

final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $passwordHasher,
        private EventPublisher $events,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): string
    {
        $email = new Email($command->email);

        if ($this->users->existsWithEmail($email)) {
            throw EmailAlreadyExists::withEmail($email->toString());
        }

        $user = User::register(UserId::generate(), $email, $this->passwordHasher->hash($command->plainPassword));

        $this->users->save($user);
        $this->events->publish($user->pullDomainEvents());

        return $user->id()->toString();
    }
}
