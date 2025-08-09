<?php

namespace Raketa\BackendTestTask\Domain;

interface ProductRepositoryInterface
{
    public function getByUuid(string $uuid): ?Product;
    public function getByCategory(string $category): array;
}