<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Messenger;

use App\Auth\Application\Command\CommandBus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class CommandBusAdapter implements CommandBus
{
    public function __construct(
        #[Autowire(service: 'command.bus')]
        private MessageBusInterface $bus,
    ) {
    }

    public function dispatch(object $command): mixed
    {
        try {
            /** @var Envelope $envelope */
            $envelope = $this->bus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
