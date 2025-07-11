<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Logger;
use Psr\Log\LogLevel;
use Stringable;

class ApplicationLogger implements LoggerInterface
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logDatabaseOperation(string $operation, array $context = []): void
    {
        $this->logger->info("Database operation: {$operation}", array_merge($context, [
            'category' => 'database',
            'operation' => $operation,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logCacheOperation(string $operation, array $context = []): void
    {
        $this->logger->debug("Cache operation: {$operation}", array_merge($context, [
            'category' => 'cache',
            'operation' => $operation,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $this->logger->warning("Security event: {$event}", array_merge($context, [
            'category' => 'security',
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logApplicationEvent(string $event, array $context = []): void
    {
        $this->logger->info("Application event: {$event}", array_merge($context, [
            'category' => 'application',
            'event' => $event,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $level = $duration > 1.0 ? LogLevel::WARNING : LogLevel::INFO;

        $this->logger->log($level, "Performance: {$operation} took {$duration}s", array_merge($context, [
            'category' => 'performance',
            'operation' => $operation,
            'duration' => $duration,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logBusinessEvent(string $event, array $context = []): void
    {
        $this->logger->info("Business event: {$event}", array_merge($context, [
            'category' => 'business',
            'event' => $event,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logRequest(string $method, string $path, array $context = []): void
    {
        $this->logger->info("Request: {$method} {$path}", array_merge($context, [
            'category' => 'request',
            'method' => $method,
            'path' => $path,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logResponse(int $statusCode, string $method, string $path, array $context = []): void
    {
        $level = $statusCode >= 400 ? LogLevel::WARNING : LogLevel::INFO;

        $this->logger->log($level, "Response: {$statusCode} for {$method} {$path}", array_merge($context, [
            'category' => 'response',
            'status_code' => $statusCode,
            'method' => $method,
            'path' => $path,
        ]));
    }

    // PSR-3 LoggerInterface methods
    /**
     * @param array<string, mixed> $context
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
