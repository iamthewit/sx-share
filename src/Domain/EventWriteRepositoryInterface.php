<?php

namespace StockExchange\Domain;

use Prooph\Common\Messaging\DomainEvent;

interface EventWriteRepositoryInterface
{
    public function storeEvent(DomainEvent $event): void;
}