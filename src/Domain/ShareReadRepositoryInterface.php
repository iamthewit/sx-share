<?php


namespace StockExchange\Domain;


interface ShareReadRepositoryInterface
{
    public function findById(string $id): Share;
}