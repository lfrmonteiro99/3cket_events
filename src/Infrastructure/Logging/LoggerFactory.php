<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LoggerFactory
{
    private const LOG_DIR = __DIR__ . '/../../../logs';
    private const DEFAULT_LOG_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    public static function createFromEnvironment(string $name = 'app'): Logger
    {
        $logLevel = self::getLogLevelFromEnvironment();
        $logFormat = $_ENV['LOG_FORMAT'] ?? 'line';
        $logHandler = $_ENV['LOG_HANDLER'] ?? 'file';

        return self::createLogger($name, $logLevel, $logFormat, $logHandler);
    }

    public static function createLogger(
        string $name,
        Level $level = Level::Info,
        string $format = 'line',
        string $handler = 'file'
    ): Logger {
        $logger = new Logger($name);

        $monologHandler = match ($handler) {
            'stdout' => self::createStdoutHandler($level, $format),
            'stderr' => self::createStderrHandler($level, $format),
            'rotating' => self::createRotatingFileHandler($name, $level, $format),
            'null' => self::createNullHandler(),
            default => self::createFileHandler($name, $level, $format),
        };

        $logger->pushHandler($monologHandler);

        return $logger;
    }

    public static function createApplicationLogger(): Logger
    {
        return self::createFromEnvironment('application');
    }

    public static function createDatabaseLogger(): Logger
    {
        return self::createFromEnvironment('database');
    }

    public static function createCacheLogger(): Logger
    {
        return self::createFromEnvironment('cache');
    }

    public static function createSecurityLogger(): Logger
    {
        return self::createFromEnvironment('security');
    }

    public static function createErrorLogger(): Logger
    {
        $logLevel = self::getLogLevelFromEnvironment();
        $logFormat = $_ENV['LOG_FORMAT'] ?? 'line';
        $logHandler = $_ENV['LOG_HANDLER'] ?? 'file';

        return self::createLogger('errors', $logLevel, $logFormat, $logHandler);
    }

    public static function createPerformanceLogger(): Logger
    {
        $logLevel = self::getLogLevelFromEnvironment();
        $logFormat = $_ENV['LOG_FORMAT'] ?? 'line';
        $logHandler = $_ENV['LOG_HANDLER'] ?? 'file';

        return self::createLogger('performance', $logLevel, $logFormat, $logHandler);
    }

    public static function createRequestLogger(): Logger
    {
        $logLevel = self::getLogLevelFromEnvironment();
        $logFormat = $_ENV['LOG_FORMAT'] ?? 'line';
        $logHandler = $_ENV['LOG_HANDLER'] ?? 'file';

        return self::createLogger('requests', $logLevel, $logFormat, $logHandler);
    }

    private static function createFileHandler(string $name, Level $level, string $format): StreamHandler
    {
        self::ensureLogDirectoryExists();

        $logFile = self::LOG_DIR . "/{$name}.log";
        $handler = new StreamHandler($logFile, $level);
        $handler->setFormatter(self::createFormatter($format));

        return $handler;
    }

    private static function createRotatingFileHandler(string $name, Level $level, string $format): RotatingFileHandler
    {
        self::ensureLogDirectoryExists();

        $logFile = self::LOG_DIR . "/{$name}.log";
        $handler = new RotatingFileHandler($logFile, 7, $level); // Keep 7 days of logs
        $handler->setFormatter(self::createFormatter($format));

        return $handler;
    }

    private static function createStdoutHandler(Level $level, string $format): StreamHandler
    {
        $handler = new StreamHandler('php://stdout', $level);
        $handler->setFormatter(self::createFormatter($format));

        return $handler;
    }

    private static function createStderrHandler(Level $level, string $format): StreamHandler
    {
        $handler = new StreamHandler('php://stderr', $level);
        $handler->setFormatter(self::createFormatter($format));

        return $handler;
    }

    private static function createNullHandler(): NullHandler
    {
        return new NullHandler();
    }

    private static function createFormatter(string $format): LineFormatter|JsonFormatter
    {
        return match ($format) {
            'json' => new JsonFormatter(),
            default => new LineFormatter(self::DEFAULT_LOG_FORMAT, 'Y-m-d H:i:s', true, true),
        };
    }

    private static function getLogLevelFromEnvironment(): Level
    {
        $logLevel = strtoupper($_ENV['LOG_LEVEL'] ?? 'INFO');

        return match ($logLevel) {
            'DEBUG' => Level::Debug,
            'INFO' => Level::Info,
            'NOTICE' => Level::Notice,
            'WARNING' => Level::Warning,
            'ERROR' => Level::Error,
            'CRITICAL' => Level::Critical,
            'ALERT' => Level::Alert,
            'EMERGENCY' => Level::Emergency,
            default => Level::Info,
        };
    }

    private static function ensureLogDirectoryExists(): void
    {
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0o755, true);
        }
    }
}
