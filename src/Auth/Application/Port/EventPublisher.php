<?php

declare(strict_types=1);

namespace App\Auth\Application\Port;

use App\Auth\Domain\Event\DomainEvent;

interface EventPublisher
{
    /** @param list<DomainEvent> $events */
    public function publish(array $events): void;
}
