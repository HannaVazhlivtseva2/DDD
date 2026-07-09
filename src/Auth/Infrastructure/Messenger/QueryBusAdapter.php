<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Messenger;

use App\Auth\Application\Query\QueryBus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class QueryBusAdapter implements QueryBus
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $bus,
    ) {
    }

    public function ask(object $query): mixed
    {
        try {
            /** @var Envelope $envelope */
            $envelope = $this->bus->dispatch($query);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
