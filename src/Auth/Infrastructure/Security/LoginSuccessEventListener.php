<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\Port\EventPublisher;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final readonly class LoginSuccessEventListener
{
    public function __construct(private EventPublisher $events)
    {
    }

    #[AsEventListener]
    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof SecurityUser) {
            return;
        }

        $domainUser = $user->domainUser();
        $domainUser->recordLogin();
        $this->events->publish($domainUser->pullDomainEvents());
    }
}
