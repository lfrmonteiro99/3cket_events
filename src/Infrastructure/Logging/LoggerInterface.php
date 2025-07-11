<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    /**
     * Log database operations.
     *
     * @param array<string, mixed> $context
     */
    public function logDatabaseOperation(string $operation, array $context = []): void;

    /**
     * Log cache operations.
     *
     * @param array<string, mixed> $context
     */
    public function logCacheOperation(string $operation, array $context = []): void;

    /**
     * Log security events.
     *
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(string $event, array $context = []): void;

    /**
     * Log application events.
     *
     * @param array<string, mixed> $context
     */
    public function logApplicationEvent(string $event, array $context = []): void;

    /**
     * Log performance metrics.
     *
     * @param array<string, mixed> $context
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void;

    /**
     * Log business events.
     *
     * @param array<string, mixed> $context
     */
    public function logBusinessEvent(string $event, array $context = []): void;

    /**
     * Log HTTP requests.
     *
     * @param array<string, mixed> $context
     */
    public function logRequest(string $method, string $path, array $context = []): void;

    /**
     * Log HTTP responses.
     *
     * @param array<string, mixed> $context
     */
    public function logResponse(int $statusCode, string $method, string $path, array $context = []): void;
}
