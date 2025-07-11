<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Logger;
use Stringable;

class SpecializedLogger implements LoggerInterface
{
    private Logger $errorLogger;
    private Logger $performanceLogger;
    private Logger $requestLogger;
    private Logger $applicationLogger;

    public function __construct()
    {
        $this->errorLogger = LoggerFactory::createErrorLogger();
        $this->performanceLogger = LoggerFactory::createPerformanceLogger();
        $this->requestLogger = LoggerFactory::createRequestLogger();
        $this->applicationLogger = LoggerFactory::createApplicationLogger();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logDatabaseOperation(string $operation, array $context = []): void
    {
        $this->applicationLogger->info("Database operation: {$operation}", array_merge($context, [
            'category' => 'database',
            'operation' => $operation,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logCacheOperation(string $operation, array $context = []): void
    {
        $this->applicationLogger->debug("Cache operation: {$operation}", array_merge($context, [
            'category' => 'cache',
            'operation' => $operation,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $this->errorLogger->warning("Security event: {$event}", array_merge($context, [
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
        $this->applicationLogger->info("Application event: {$event}", array_merge($context, [
            'category' => 'application',
            'event' => $event,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $level = $duration > 1.0 ? 'warning' : 'info';

        $this->performanceLogger->log($level, "Performance: {$operation} took {$duration}s", array_merge($context, [
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
        $this->applicationLogger->info("Business event: {$event}", array_merge($context, [
            'category' => 'business',
            'event' => $event,
        ]));
    }

    /**
     * @param array<string, mixed> $context
     */
    public function logRequest(string $method, string $path, array $context = []): void
    {
        $this->requestLogger->info("Request: {$method} {$path}", array_merge($context, [
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
        $level = $statusCode >= 400 ? 'warning' : 'info';

        $this->requestLogger->log($level, "Response: {$statusCode} for {$method} {$path}", array_merge($context, [
            'category' => 'response',
            'status_code' => $statusCode,
            'method' => $method,
            'path' => $path,
        ]));
    }

    // PSR-3 LoggerInterface methods - route to appropriate logger
    /**
     * @param array<string, mixed> $context
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->errorLogger->emergency($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->errorLogger->alert($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->errorLogger->critical($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->errorLogger->error($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->errorLogger->warning($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->applicationLogger->notice($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->applicationLogger->info($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->applicationLogger->debug($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        // Route to appropriate logger based on level
        $levelString = is_string($level) ? strtolower($level) : $level->name ?? 'info';

        match ($levelString) {
            'emergency', 'alert', 'critical', 'error', 'warning' => $this->errorLogger->log($level, $message, $context),
            default => $this->applicationLogger->log($level, $message, $context),
        };
    }
}
