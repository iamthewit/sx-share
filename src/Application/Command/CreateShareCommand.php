<?php

namespace StockExchange\Application\Command;

use Ramsey\Uuid\UuidInterface;
use StockExchange\Domain\Symbol;

class CreateShareCommand
{
    private UuidInterface $exchangeId;
    private UuidInterface $shareId;
    private Symbol $symbol;

    /**
     * CreateShareCommand constructor.
     *
     * @param UuidInterface $exchangeId
     * @param UuidInterface $shareId
     * @param Symbol        $symbol
     */
    public function __construct(UuidInterface $exchangeId, UuidInterface $shareId, Symbol $symbol)
    {
        $this->exchangeId = $exchangeId;
        $this->shareId = $shareId;
        $this->symbol  = $symbol;
    }

    public function exchangeId(): UuidInterface
    {
        return $this->exchangeId;
    }

    public function shareId(): UuidInterface
    {
        return $this->shareId;
    }

    public function symbol(): Symbol
    {
        return $this->symbol;
    }
}
