<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private Redis $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key)
    {
        try {
            $value = $this->redis->get($key);
            if ($value === false) {
                return null;
            }

            return unserialize($value);
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws RedisException
     */
    public function has($key): bool
    {
        return $this->redis->exists($key);
    }
}
