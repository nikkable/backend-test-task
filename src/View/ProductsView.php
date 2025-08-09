<?php

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Product;

readonly class ProductsView
{

    public function toArray(array $products): array
    {
        return array_map(
            fn (Product $product) => [
                'id' => $product->getId(),
                'uuid' => $product->getUuid(),
                'name' => $product->getName(),
                'thumbnail' => $product->getThumbnail(),
                'price' => $product->getPrice(),
            ],
            $products
        );
    }
}
