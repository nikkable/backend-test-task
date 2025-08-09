<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

class ConnectorException extends \Exception
{
    public function __construct(
        protected $message,
        protected $code,
        protected ?\Throwable $previous,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
