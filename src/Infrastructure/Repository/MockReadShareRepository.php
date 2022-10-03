<?php


namespace StockExchange\Infrastructure\Repository;

use Ramsey\Uuid\Uuid;
use StockExchange\Domain\Share;
use StockExchange\Domain\Symbol;

/**
 * Class MockReadShareRepository
 * @package StockExchange\Infrastructure\Repository
 */
class MockReadShareRepository implements \StockExchange\Domain\ShareReadRepositoryInterface
{

    public function findById(string $id): Share
    {
        return Share::fromValues(
            Uuid::uuid4(),
            Symbol::fromValue('FOO'),
            Uuid::uuid4()
        );
    }
}