<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Messenger;

use App\Auth\Application\Port\EventPublisher;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class EventPublisherAdapter implements EventPublisher
{
    public function __construct(
        #[Autowire(service: 'event.bus')]
        private MessageBusInterface $bus,
    ) {
    }

    public function publish(array $events): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch($event);
        }
    }
}
