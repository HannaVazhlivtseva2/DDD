<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\UpdateAvatar;

use App\Auth\Application\Port\EventPublisher;
use App\Auth\Domain\Exception\UserNotFound;
use App\Auth\Domain\Model\UserId;
use App\Auth\Domain\Repository\UserRepository;

final readonly class UpdateAvatarHandler
{
    public function __construct(
        private UserRepository $users,
        private EventPublisher $events,
    ) {
    }

    public function __invoke(UpdateAvatarCommand $command): void
    {
        $user = $this->users->findById(UserId::fromString($command->userId));

        if (null === $user) {
            throw UserNotFound::withId($command->userId);
        }

        $user->changeAvatar($command->filename);

        $this->users->save($user);
        $this->events->publish($user->pullDomainEvents());
    }
}
