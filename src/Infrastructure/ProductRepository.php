<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Raketa\BackendTestTask\Domain\Product;
use Raketa\BackendTestTask\Domain\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function getByUuid(string $uuid): ?Product
    {
        $row = $this->connection->fetchAssociative(
            "SELECT * FROM products WHERE uuid = :uuid",
            ['uuid' => $uuid]
        );

        if (empty($row)) {
            return null;
        }

        return $this->make($row);
    }

    /**
     * @throws Exception
     */
    public function getByCategory(string $category): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT * FROM products WHERE is_active = 1 AND category = :category",
            ['category' => $category]
        );

        return array_map(
            static fn(array $row): Product => $this->make($row),
            $rows
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
