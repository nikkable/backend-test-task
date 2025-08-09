<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Infrastructure\Connector;

class CartManager
{
    private Connector $connector;
    private LoggerInterface $logger;

    public function __construct(Connector $connector, LoggerInterface $logger)
    {
        $this->connector = $connector;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(session_id(), $cart);
        } catch (Exception $e) {
            $this->logger->error('Error saving cart to Redis', ['exception' => $e->getMessage()]);
        }
    }

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        try {
            $cart = $this->connector->get(session_id());
            if ($cart instanceof Cart) {
                return $cart;
            }
        } catch (Exception $e) {
            $this->logger->error('Error getting cart from Redis', ['exception' => $e->getMessage()]);
        }

        $customer = new Customer(
            1,
            'Test',
            'User',
            '',
            'test.user@example.com'
        );
        return new Cart(session_id(), $customer, 'credit_card', []);
    }
}
