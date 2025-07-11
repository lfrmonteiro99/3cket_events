<?php

declare(strict_types=1);

namespace App\Service;

use App\Application\UseCase\GetAllEventsUseCase;
use App\Application\UseCase\GetEventByIdUseCase;
use App\Application\UseCase\GetPaginatedEventsUseCase;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheFactory;
use App\Infrastructure\Cache\CacheInterface;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\DataSource\DataSourceFactory;
use App\Infrastructure\DataSource\DataSourceStrategy;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Repository\CsvEventRepository;
use App\Infrastructure\Repository\DatabaseEventRepository;
use App\Presentation\Controller\EventController;
use Dotenv\Dotenv;
use PDO;

class Container
{
    /** @var array<string, callable> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new \InvalidArgumentException("Service not bound: {$abstract}");
        }

        $instance = $this->bindings[$abstract]($this);
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function configure(): void
    {
        // Load .env file configuration
        $this->loadEnvironmentConfig();

        // Bind cache service using Strategy pattern
        $this->bind(CacheInterface::class, function (Container $container) {
            return CacheFactory::createFromEnvironment();
        });

        // Bind database connection factory
        $this->bind(DatabaseConnection::class, function (Container $container) {
            return DatabaseConnection::fromEnvironment();
        });

        // Bind PDO instance - this is where connection pooling happens
        // Container acts as singleton manager, but with dependency injection
        $this->bind(PDO::class, function (Container $container) {
            $dbConnection = $container->get(DatabaseConnection::class);

            return $dbConnection->createConnection();
        });

        // Bind data source strategy
        $this->bind(DataSourceStrategy::class, function (Container $container) {
            $strategyValue = $_ENV['DATA_SOURCE_STRATEGY'] ?? 'auto';
            return DataSourceStrategy::fromString($strategyValue);
        });

        // Bind data source factory
        $this->bind(DataSourceFactory::class, function (Container $container) {
            $strategy = $container->get(DataSourceStrategy::class);
            return new DataSourceFactory($container, $strategy);
        });

        // Bind base repository (without caching) - using strategy pattern
        $this->bind('BaseEventRepository', function (Container $container) {
            $factory = $container->get(DataSourceFactory::class);
            return $factory->createRepository();
        });

        // Bind repository with caching - this is what the application uses
        $this->bind(EventRepositoryInterface::class, function (Container $container) {
            $baseRepository = $container->get('BaseEventRepository');
            $cache = $container->get(CacheInterface::class);

            // Get configuration from environment or use defaults
            $ttl = (int) ($_ENV['CACHE_TTL'] ?? 3600);
            $prefix = $_ENV['CACHE_PREFIX'] ?? 'events:';

            // Wrap the base repository with caching
            return new CachedEventRepository(
                $baseRepository,
                $cache,
                $ttl,
                $prefix
            );
        });

        // Bind Use Cases
        $this->bind(GetAllEventsUseCase::class, function (Container $container) {
            return new GetAllEventsUseCase($container->get(EventRepositoryInterface::class));
        });

        $this->bind(GetEventByIdUseCase::class, function (Container $container) {
            return new GetEventByIdUseCase($container->get(EventRepositoryInterface::class));
        });

        $this->bind(GetPaginatedEventsUseCase::class, function (Container $container) {
            return new GetPaginatedEventsUseCase($container->get(EventRepositoryInterface::class));
        });

        // Bind validators
        $this->bind(PaginationValidator::class, function (Container $container) {
            return new PaginationValidator();
        });

        $this->bind(EventIdValidator::class, function (Container $container) {
            return new EventIdValidator();
        });

        // Bind controller (ResponseManager created fresh per request)
        $this->bind(EventController::class, function (Container $container) {
            return new EventController(
                $container->get(GetAllEventsUseCase::class),
                $container->get(GetEventByIdUseCase::class),
                $container->get(GetPaginatedEventsUseCase::class),
                $container->get(EventRepositoryInterface::class),
                $container->get(PaginationValidator::class),
                $container->get(EventIdValidator::class),
                ResponseManager::createFromRequest() // Fresh instance per request
            );
        });
    }

    /**
     * Load environment configuration from .env file.
     */
    private function loadEnvironmentConfig(): void
    {
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->safeLoad();
        } catch (\Exception $e) {
            error_log('Could not load .env file: ' . $e->getMessage());
        }
    }
}
