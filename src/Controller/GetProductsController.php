<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Domain\ProductRepositoryInterface;
use Raketa\BackendTestTask\Exception\InvalidArgumentException;
use Raketa\BackendTestTask\Exception\ResourceNotFoundException;
use Raketa\BackendTestTask\View\ProductsView;

readonly class GetProductsController
{
    public function __construct(
        private ProductsView $productsView,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $category = $rawRequest['category'] ?? null;

        if (!$category) {
            throw new InvalidArgumentException('Category parameter is missing');
        }

        $products = $this->productRepository->getByCategory($category);

        if (empty($products)) {
            throw new ResourceNotFoundException('Products not found for this category');
        }

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                $this->productsView->toArray($products),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
