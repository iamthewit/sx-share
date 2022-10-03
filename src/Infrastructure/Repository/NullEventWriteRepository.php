<?php


namespace StockExchange\Infrastructure\Repository;

use Prooph\Common\Messaging\DomainEvent;
use StockExchange\Domain\EventWriteRepositoryInterface;

/**
 * Class NullEventWriteRepository
 * @package StockExchange\Domain
 */
class NullEventWriteRepository implements EventWriteRepositoryInterface
{
    public function storeEvent(DomainEvent $event): void
    {
    }
}