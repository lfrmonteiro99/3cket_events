<?php

declare(strict_types=1);

namespace App\Infrastructure\DataSource;

use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Repository\CsvEventRepository;
use App\Infrastructure\Repository\DatabaseEventRepository;
use App\Service\Container;
use PDO;

class DataSourceFactory
{
    public function __construct(
        private readonly Container $container,
        private readonly DataSourceStrategy $strategy
    ) {
    }

    public function createRepository(): EventRepositoryInterface
    {
        return match ($this->strategy) {
            DataSourceStrategy::DATABASE_FIRST => $this->createDatabaseFirstRepository(),
            DataSourceStrategy::CSV_FIRST => $this->createCsvFirstRepository(),
            DataSourceStrategy::DATABASE_ONLY => $this->createDatabaseOnlyRepository(),
            DataSourceStrategy::CSV_ONLY => $this->createCsvOnlyRepository(),
            DataSourceStrategy::AUTO => $this->createAutoRepository(),
        };
    }

    private function createDatabaseFirstRepository(): EventRepositoryInterface
    {
        try {
            $pdo = $this->container->get(PDO::class);
            error_log('DataSourceFactory: Using DATABASE_FIRST strategy - database available');
            return new DatabaseEventRepository($pdo);
        } catch (\Exception $e) {
            error_log('DataSourceFactory: Database unavailable, falling back to CSV: ' . $e->getMessage());
            return new CsvEventRepository($this->getCsvPath());
        }
    }

    private function createCsvFirstRepository(): EventRepositoryInterface
    {
        try {
            $csvPath = $this->getCsvPath();
            if (!file_exists($csvPath)) {
                throw new \RuntimeException("CSV file not found: {$csvPath}");
            }
            error_log('DataSourceFactory: Using CSV_FIRST strategy - CSV file available');
            return new CsvEventRepository($csvPath);
        } catch (\Exception $e) {
            error_log('DataSourceFactory: CSV unavailable, falling back to database: ' . $e->getMessage());
            $pdo = $this->container->get(PDO::class);
            return new DatabaseEventRepository($pdo);
        }
    }

    private function createDatabaseOnlyRepository(): EventRepositoryInterface
    {
        $pdo = $this->container->get(PDO::class);
        error_log('DataSourceFactory: Using DATABASE_ONLY strategy');
        return new DatabaseEventRepository($pdo);
    }

    private function createCsvOnlyRepository(): EventRepositoryInterface
    {
        $csvPath = $this->getCsvPath();
        error_log('DataSourceFactory: Using CSV_ONLY strategy');
        return new CsvEventRepository($csvPath);
    }

    private function createAutoRepository(): EventRepositoryInterface
    {
        // Auto-detect: prefer database if available, otherwise CSV
        try {
            $pdo = $this->container->get(PDO::class);
            
            // Test database connection
            $pdo->query('SELECT 1');
            
            error_log('DataSourceFactory: Using AUTO strategy - database is available and working');
            return new DatabaseEventRepository($pdo);
        } catch (\Exception $e) {
            error_log('DataSourceFactory: Using AUTO strategy - database unavailable, trying CSV: ' . $e->getMessage());
            
            $csvPath = $this->getCsvPath();
            if (file_exists($csvPath)) {
                error_log('DataSourceFactory: Using AUTO strategy - CSV file available');
                return new CsvEventRepository($csvPath);
            }
            
            // If both fail, throw meaningful error
            throw new \RuntimeException(
                'No data source available. Database error: ' . $e->getMessage() . 
                ', CSV file not found: ' . $csvPath
            );
        }
    }

    private function getCsvPath(): string
    {
        return __DIR__ . '/../../../data/seeds.csv';
    }

    public function getStrategy(): DataSourceStrategy
    {
        return $this->strategy;
    }

    public function getStrategyDescription(): string
    {
        return $this->strategy->getDisplayName();
    }
} 