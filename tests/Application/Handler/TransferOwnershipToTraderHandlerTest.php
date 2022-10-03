<?php

namespace App\Tests\Application\Handler;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use StockExchange\Application\Command\CreateShareCommand;
use StockExchange\Application\Command\TransferOwnershipToTraderCommand;
use StockExchange\Domain\Event\ShareOwnershipTransferred;
use StockExchange\Domain\Share;
use StockExchange\Domain\Symbol;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class TransferOwnershipToTraderHandlerTest extends KernelTestCase
{
    public function testItTransfersOwnership()
    {
        // Create Share
        $exchangeId = Uuid::uuid4();
        $shareId = Uuid::uuid4();
        $messageBus = $this->getContainer()->get(MessageBusInterface::class);
        $envelope = $messageBus->dispatch(
            new CreateShareCommand($exchangeId, $shareId, Symbol::fromValue('FOO'))
        );
        /** @var Share $share */
        $share = $envelope->last(HandledStamp::class)->getResult();
        $this->assertNull($share->ownerId());

        // Transfer Share Ownership
        $traderId = Uuid::uuid4();
        $envelope = $messageBus->dispatch(
            new TransferOwnershipToTraderCommand($exchangeId, $shareId, $traderId)
        );
        /** @var Share $share */
        $share = $envelope->last(HandledStamp::class)->getResult();

        $this->assertInstanceOf(UuidInterface::class, $share->ownerId());
        $this->assertEquals($traderId->toString(), $share->ownerId()->toString());
    }

    public function testItDispatchesAndClearsDomainEvents()
    {
        $exchangeId = Uuid::uuid4();
        $shareId = Uuid::uuid4();
        $traderId = Uuid::uuid4();
        $messageBus = $this->getContainer()->get(MessageBusInterface::class);
        $messageBus->dispatch(
            new CreateShareCommand($exchangeId, $shareId, Symbol::fromValue('FOO'))
        );
        $envelope = $messageBus->dispatch(
            new TransferOwnershipToTraderCommand($exchangeId, $shareId, $traderId)
        );
        /** @var Share $share */
        $share = $envelope->last(HandledStamp::class)->getResult();

        /* @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(2, $transport->getSent());
        $this->assertInstanceOf(ShareOwnershipTransferred::class, $transport->getSent()[1]->getMessage());

        $this->assertEmpty($share->dispatchableEvents());
    }
}
