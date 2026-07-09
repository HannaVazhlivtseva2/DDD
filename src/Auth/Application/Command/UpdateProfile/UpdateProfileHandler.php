<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\UpdateProfile;

use App\Auth\Application\Port\EventPublisher;
use App\Auth\Domain\Exception\UserNotFound;
use App\Auth\Domain\Model\Gender;
use App\Auth\Domain\Model\PhoneNumber;
use App\Auth\Domain\Model\UserId;
use App\Auth\Domain\Repository\UserRepository;

final readonly class UpdateProfileHandler
{
    public function __construct(
        private UserRepository $users,
        private EventPublisher $events,
    ) {
    }

    public function __invoke(UpdateProfileCommand $command): void
    {
        $user = $this->users->findById(UserId::fromString($command->userId));

        if (null === $user) {
            throw UserNotFound::withId($command->userId);
        }

        $gender = Gender::tryFrom($command->gender)
            ?? throw new \InvalidArgumentException(\sprintf('"%s" is not a valid gender.', $command->gender));

        $user->updateProfile($command->firstName, $command->lastName, new PhoneNumber($command->phone), $gender);

        $this->users->save($user);
        $this->events->publish($user->pullDomainEvents());
    }
}
