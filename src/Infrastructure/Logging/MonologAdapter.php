<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Logger as MonologLogger;
use Stringable;

class MonologAdapter implements LoggerInterface
{
    public function __construct(
        private readonly MonologLogger $monologLogger
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logDatabaseOperation(string $operation, array $context = []): void
    {
        $this->monologLogger->info("Database operation: {$operation}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logCacheOperation(string $operation, array $context = []): void
    {
        $this->monologLogger->info("Cache operation: {$operation}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $this->monologLogger->warning("Security event: {$event}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logApplicationEvent(string $event, array $context = []): void
    {
        $this->monologLogger->info("Application event: {$event}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $this->monologLogger->info("Performance: {$operation} took {$duration}s", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logBusinessEvent(string $event, array $context = []): void
    {
        $this->monologLogger->info("Business event: {$event}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logRequest(string $method, string $path, array $context = []): void
    {
        $this->monologLogger->info("Request: {$method} {$path}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logResponse(int $statusCode, string $method, string $path, array $context = []): void
    {
        $this->monologLogger->info("Response: {$statusCode} for {$method} {$path}", $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->emergency((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->alert((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->critical((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->error((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->warning((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->notice((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->info((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->debug((string) $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->monologLogger->log($level, (string) $message, $context);
    }
}
