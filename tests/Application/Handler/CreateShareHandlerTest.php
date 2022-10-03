<?php

namespace App\Tests\Application\Handler;

use Ramsey\Uuid\Uuid;
use StockExchange\Application\Command\CreateShareCommand;
use StockExchange\Domain\Event\AskAdded;
use StockExchange\Domain\Event\ShareCreated;
use StockExchange\Domain\Share;
use StockExchange\Domain\Symbol;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class CreateShareHandlerTest extends KernelTestCase
{
    public function testItCreatesAShare()
    {
        $messageBus = $this->getContainer()->get(MessageBusInterface::class);
        $envelope = $messageBus->dispatch(
            new CreateShareCommand(
                Uuid::uuid4(),
                Uuid::uuid4(),
                Symbol::fromValue('FOO')
            )
        );
        $share = $envelope->last(HandledStamp::class)->getResult();

        $this->assertInstanceOf(Share::class, $share);
    }

    public function testItDispatchesAndClearsDomainEvents()
    {
        $messageBus = $this->getContainer()->get(MessageBusInterface::class);
        $envelope = $messageBus->dispatch(
            new CreateShareCommand(
                Uuid::uuid4(),
                Uuid::uuid4(),
                Symbol::fromValue('FOO')
            )
        );
        /** @var Share $share */
        $share = $envelope->last(HandledStamp::class)->getResult();

        /* @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());
        $this->assertInstanceOf(ShareCreated::class, $transport->getSent()[0]->getMessage());

        $this->assertEmpty($share->dispatchableEvents());
    }
}
