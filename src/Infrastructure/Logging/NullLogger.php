<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Stringable;

class NullLogger implements LoggerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function logDatabaseOperation(string $operation, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logCacheOperation(string $operation, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logApplicationEvent(string $event, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logBusinessEvent(string $event, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logRequest(string $method, string $path, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logResponse(int $statusCode, string $method, string $path, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        // Do nothing
    }
}
