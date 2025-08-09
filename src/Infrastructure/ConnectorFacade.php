<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;

class ConnectorFacade
{
    public string $host;
    public int $port = 6379;
    public ?string $password = null;
    public ?int $dbindex = null;

    public Connector $connector;
    private LoggerInterface $logger;

    public function __construct(string $host, int $port, ?string $password, ?int $dbindex, LoggerInterface $logger)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbindex = $dbindex;
        $this->logger = $logger;
    }

    protected function build(): void
    {
        $redis = new Redis();

        try {
            $isConnected = $redis->isConnected();
            if (! $isConnected && $redis->ping('Pong')) {
                $isConnected = $redis->connect(
                    $this->host,
                    $this->port,
                );
            }
        } catch (RedisException) {
            $this->logger->error('Failed to connect to Redis', ['exception' => $e->getMessage()]);
            return;
        }

        if ($isConnected) {
            $redis->auth($this->password);
            $redis->select($this->dbindex);
            $this->connector = new Connector($redis);
        }
    }
}
