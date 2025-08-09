<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;

class ConnectorFacade
{
    public Connector $connector;
    private LoggerInterface $logger;

    /**
     * @throws ConnectorException
     * @throws RedisException
     */
    public function __construct(string $host, int $port, ?string $password, ?int $dbindex, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $redis = $this->connectToRedis($host, $port, $password, $dbindex);
        $this->connector = new Connector($redis);
    }

    /**
     * @throws ConnectorException
     * @throws RedisException
     */
    private function connectToRedis(string $host, int $port, ?string $password, ?int $dbindex): Redis
    {
        $redis = new Redis();
        try {
            if (!$redis->isConnected()) {
                $redis->connect($host, $port);
            }
        } catch (RedisException $e) {
            $this->logger->error('Failed to connect to Redis', ['exception' => $e->getMessage()]);
            throw new ConnectorException('Failed to connect to Redis', 0, $e);
        }

        if ($password !== null) {
            $redis->auth($password);
        }
        if ($dbindex !== null) {
            $redis->select($dbindex);
        }

        return $redis;
    }
}
