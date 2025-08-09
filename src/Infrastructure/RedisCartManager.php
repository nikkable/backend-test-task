<?php

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartManagerInterface;
use Raketa\BackendTestTask\Domain\Customer;
use Ramsey\Uuid\Uuid;

readonly class RedisCartManager implements CartManagerInterface
{
    private Connector $connector;
    private LoggerInterface $logger;

    public function __construct(Connector $connector, LoggerInterface $logger)
    {
        $this->connector = $connector;
        $this->logger = $logger;
    }

    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(session_id(), $cart);
        } catch (ConnectorException $e) {
            $this->logger->error('Error saving cart to Redis', ['exception' => $e->getMessage()]);
        }
    }

    public function getCart(): Cart
    {
        try {
            $cart = $this->connector->get(session_id());
            if ($cart instanceof Cart) {
                return $cart;
            }
        } catch (ConnectorException $e) {
            $this->logger->error('Error getting cart from Redis', ['exception' => $e->getMessage()]);
        }

        $customer = new Customer(1, 'Test', 'User', '', 'test.user@example.com');
        return new Cart(Uuid::uuid4()->toString(), $customer, 'credit_card', []);
    }
}