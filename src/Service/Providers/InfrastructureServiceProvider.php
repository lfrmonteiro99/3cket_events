<?php

declare(strict_types=1);

namespace App\Service\Providers;

use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheFactory;
use App\Infrastructure\Cache\CacheInterface;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\DataSource\DataSourceFactory;
use App\Infrastructure\DataSource\DataSourceStrategy;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Logging\SpecializedLogger;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Service\Container;
use App\Service\ServiceProvider;
use Dotenv\Dotenv;
use PDO;

class InfrastructureServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $this->loadEnvironmentConfig();
        $this->registerLogging($container);
        $this->registerCache($container);
        $this->registerDatabase($container);
        $this->registerRepositories($container);
    }

    private function loadEnvironmentConfig(): void
    {
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
            $dotenv->safeLoad();
        } catch (\Exception $e) {
            error_log('Could not load .env file: ' . $e->getMessage());
        }
    }

    private function registerLogging(Container $container): void
    {
        $container->bind(LoggerInterface::class, function () {
            return new SpecializedLogger();
        });
    }

    private function registerCache(Container $container): void
    {
        $container->bind(CacheInterface::class, fn () => CacheFactory::createFromEnvironment());
    }

    private function registerDatabase(Container $container): void
    {
        $container->bind(DatabaseConnection::class, fn () => DatabaseConnection::fromEnvironment());

        $container->bind(PDO::class, function (Container $container) {
            $dbConnection = $container->get(DatabaseConnection::class);

            return $dbConnection->createConnection();
        });
    }

    private function registerRepositories(Container $container): void
    {
        // Data source strategy
        $container->bind(DataSourceStrategy::class, function () {
            $strategyValue = $_ENV['DATA_SOURCE_STRATEGY'] ?? 'auto';

            return DataSourceStrategy::fromString($strategyValue);
        });

        // Data source factory
        $container->bind(DataSourceFactory::class, function (Container $container) {
            $strategy = $container->get(DataSourceStrategy::class);

            return new DataSourceFactory($container, $strategy);
        });

        // Base repository (without caching)
        $container->bind('BaseEventRepository', function (Container $container) {
            $factory = $container->get(DataSourceFactory::class);

            return $factory->createRepository();
        });

        // Repository with caching (main implementation)
        $container->bind(EventRepositoryInterface::class, function (Container $container) {
            $baseRepository = $container->get('BaseEventRepository');
            $cache = $container->get(CacheInterface::class);
            $logger = $container->get(LoggerInterface::class);

            $ttl = (int) ($_ENV['CACHE_TTL'] ?? 3600);
            $prefix = $_ENV['CACHE_PREFIX'] ?? 'events:';

            return new CachedEventRepository($baseRepository, $cache, $ttl, $prefix, $logger);
        });
    }
}
