<?php

namespace StockExchange\Application\Handler;

use StockExchange\Application\Command\CreateShareCommand;
use StockExchange\Domain\Share;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateShareHandler implements MessageHandlerInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(CreateShareCommand $command)
    {
        $share = Share::create(
            $command->shareId(),
            $command->symbol()
        );

        foreach ($share->dispatchableEvents() as $event) {
            $this->messageBus->dispatch($event);
        }

        $share->clearDispatchableEvents();

        // TODO: store in repo

        return $share; // TODO: remove this
    }
}

