<?php

declare(strict_types=1);

namespace App\Service\Providers;

use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheAnalytics;
use App\Infrastructure\Cache\CacheInterface;
use App\Infrastructure\Cache\CacheWarmer;
use App\Infrastructure\Cache\InMemoryCache;
use App\Infrastructure\Cache\NullCache;
use App\Infrastructure\Cache\RedisCache;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\DataSource\DataSourceStrategy;
use App\Infrastructure\Logging\ApplicationLogger;
use App\Infrastructure\Logging\LoggerFactory;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Logging\MonologAdapter;
use App\Infrastructure\Logging\SpecializedLogger;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Infrastructure\Repository\CsvEventRepository;
use App\Infrastructure\Repository\DatabaseEventRepository;
use App\Infrastructure\Response\ResponseFormatStrategy;
use App\Infrastructure\Response\ResponseFormatterInterface;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Validation\ValidatorBag;
use App\Infrastructure\Validation\ValidatorInterface;
use App\Service\Container;
use App\Service\ServiceProvider;
use Redis;

class InfrastructureServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        // Database
        $container->singleton(DatabaseConnection::class, function () {
            return DatabaseConnection::fromEnvironment();
        });

        // Cache
        $container->singleton(CacheInterface::class, function () {
            $strategy = $_ENV['CACHE_STRATEGY'] ?? 'auto';

            if ($strategy === 'redis') {
                $redis = new Redis();
                $host = $_ENV['REDIS_HOST'] ?? 'redis';
                $port = (int) ($_ENV['REDIS_PORT'] ?? 6379);
                $password = $_ENV['REDIS_PASSWORD'] ?? null;
                $database = (int) ($_ENV['REDIS_DATABASE'] ?? 0);

                $redis->connect($host, $port, 2.5);

                if ($password) {
                    $redis->auth($password);
                }

                if ($database > 0) {
                    $redis->select($database);
                }

                return new RedisCache($redis);
            }

            if ($strategy === 'memory') {
                return new InMemoryCache();
            }

            if ($strategy === 'null') {
                return new NullCache();
            }

            // Auto strategy - try Redis, fallback to memory
            try {
                $redis = new Redis();
                $host = $_ENV['REDIS_HOST'] ?? 'redis';
                $port = (int) ($_ENV['REDIS_PORT'] ?? 6379);
                $password = $_ENV['REDIS_PASSWORD'] ?? null;
                $database = (int) ($_ENV['REDIS_DATABASE'] ?? 0);

                $redis->connect($host, $port, 2.5);

                if ($password) {
                    $redis->auth($password);
                }

                if ($database > 0) {
                    $redis->select($database);
                }

                return new RedisCache($redis);
            } catch (\Exception $e) {
                error_log('Redis connection failed, falling back to in-memory cache: ' . $e->getMessage());

                return new InMemoryCache();
            }
        });

        // Cache Analytics
        $container->singleton(CacheAnalytics::class, function (Container $container) {
            return new CacheAnalytics(
                $container->get(CacheInterface::class),
                $container->get(LoggerInterface::class)
            );
        });

        // Cache Warmer
        $container->singleton(CacheWarmer::class, function (Container $container) {
            return new CacheWarmer(
                $container->get(CacheInterface::class),
                $container->get(EventRepositoryInterface::class),
                $container->get(LoggerInterface::class)
            );
        });

        // Cache Manager
        $container->singleton(\App\Infrastructure\Cache\CacheManager::class, function (Container $container) {
            return new \App\Infrastructure\Cache\CacheManager(
                $container->get(CachedEventRepository::class),
                $container->get(CacheWarmer::class),
                $container->get(CacheAnalytics::class),
                $container->get(LoggerInterface::class)
            );
        });

        // Data Source
        $container->singleton(DataSourceStrategy::class, function () {
            $strategy = $_ENV['DATA_SOURCE_STRATEGY'] ?? 'auto';

            return DataSourceStrategy::fromString($strategy);
        });

        // Logging
        $container->singleton(LoggerInterface::class, function () {
            $strategy = $_ENV['LOGGING_STRATEGY'] ?? 'auto';

            $monologLogger = LoggerFactory::createFromEnvironment();

            return new MonologAdapter($monologLogger);
        });

        $container->singleton(ApplicationLogger::class, function (Container $container) {
            return new ApplicationLogger($container->get(LoggerInterface::class));
        });

        $container->singleton(SpecializedLogger::class, function (Container $container) {
            return new SpecializedLogger();
        });

        // Repositories
        $container->singleton(CsvEventRepository::class, function () {
            $csvPath = $_ENV['CSV_PATH'] ?? 'data/seeds.csv';

            return new CsvEventRepository($csvPath);
        });

        $container->singleton(DatabaseEventRepository::class, function (Container $container) {
            $dbConnection = $container->get(DatabaseConnection::class);

            return new DatabaseEventRepository(
                $dbConnection->createConnection(),
                $container->get(LoggerInterface::class)
            );
        });

        $container->singleton(CachedEventRepository::class, function (Container $container) {
            $strategy = $_ENV['DATA_SOURCE_STRATEGY'] ?? 'auto';
            $baseRepository = null;

            if ($strategy === 'csv' || ($strategy === 'auto' && !file_exists($_ENV['CSV_PATH'] ?? 'data/seeds.csv'))) {
                $baseRepository = $container->get(CsvEventRepository::class);
            } else {
                $baseRepository = $container->get(DatabaseEventRepository::class);
            }

            return new CachedEventRepository(
                $baseRepository,
                $container->get(CacheInterface::class),
                $container->get(CacheAnalytics::class),
                (int) ($_ENV['CACHE_TTL'] ?? 3600)
            );
        });

        // Response Formatters
        $container->singleton(ResponseFormatStrategy::class, function () {
            $strategy = $_ENV['RESPONSE_FORMAT_STRATEGY'] ?? 'auto';

            return ResponseFormatStrategy::fromString($strategy);
        });

        $container->singleton(ResponseFormatterInterface::class, function (Container $container) {
            $strategy = $container->get(ResponseFormatStrategy::class);

            return $strategy->getFormatter();
        });

        $container->singleton(ResponseManager::class, function (Container $container) {
            return new ResponseManager($container->get(ResponseFormatStrategy::class));
        });

        // Validators
        $container->singleton(ValidatorInterface::class, function () {
            return new PaginationValidator();
        });

        $container->singleton(EventIdValidator::class, function () {
            return new EventIdValidator();
        });

        $container->singleton(PaginationValidator::class, function () {
            return new PaginationValidator();
        });

        $container->singleton(ValidatorBag::class, function (Container $container) {
            return new ValidatorBag(
                $container->get(EventIdValidator::class),
                $container->get(PaginationValidator::class)
            );
        });

        // Bind the main repository interface to the cached repository
        $container->singleton(EventRepositoryInterface::class, function (Container $container) {
            return $container->get(CachedEventRepository::class);
        });
    }
}
