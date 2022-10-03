<?php

namespace StockExchange\Application\Handler;

use StockExchange\Application\Command\TransferOwnershipToTraderCommand;
use StockExchange\Domain\ShareReadRepositoryInterface;
use StockExchange\Domain\ShareWriteRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class AllocateShareToTraderHandler
 * @package StockExchange\Application\Handler
 */
class TransferOwnershipToTraderHandler implements MessageHandlerInterface
{
    private MessageBusInterface          $messageBus;
    private ShareReadRepositoryInterface $shareReadRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        ShareReadRepositoryInterface $shareReadRepository,
        ShareWriteRepositoryInterface $shareWriteRepository
    ) {
        $this->messageBus          = $messageBus;
        $this->shareReadRepository = $shareReadRepository;
    }

    public function __invoke(TransferOwnershipToTraderCommand $command)
    {
        $share = $this->shareReadRepository->findById($command->shareId()->toString());

        $share->transferOwnershipToTrader($command->traderId());

        // dispatch aggregate events
        foreach ($share->dispatchableEvents() as $event) {
            $this->messageBus->dispatch($event);
        }

        $share->clearDispatchableEvents();

        // TODO: store changes in share repo

        return $share; // TODO: remove this
    }
}
