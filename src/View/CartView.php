<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\ProductRepositoryInterface;

readonly class CartView
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $cartTotal = 0;
        $data['items'] = [];
        foreach ($cart->getItems() as $item) {
            $itemTotal = $item->getPrice() * $item->getQuantity();
            $cartTotal += $itemTotal;
            $product = $this->productRepository->getByUuid($item->getProductUuid());

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $itemTotal,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product?->getId(),
                    'uuid' => $product?->getUuid(),
                    'name' => $product?->getName(),
                    'thumbnail' => $product?->getThumbnail(),
                    'price' => $product?->getPrice(),
                ],
            ];
        }

        $data['total'] = $cartTotal;

        return $data;
    }
}
