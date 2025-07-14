<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

class CacheFactory
{
    public static function createFromStrategy(CacheStrategy $strategy): CacheInterface
    {
        return match ($strategy) {
            CacheStrategy::REDIS => self::createRedisCache(),
            CacheStrategy::MEMORY, CacheStrategy::IN_MEMORY => new InMemoryCache(),
            CacheStrategy::NONE, CacheStrategy::NULL => new NullCache(),
            CacheStrategy::AUTO => self::createAutoCache(),
        };
    }

    public static function createFromEnvironment(): CacheInterface
    {
        $strategyValue = $_ENV['CACHE_STRATEGY'] ?? $_ENV['CACHE_DRIVER'] ?? 'auto';
        $strategy = CacheStrategy::fromString($strategyValue);

        return self::createFromStrategy($strategy);
    }

    private static function createRedisCache(): CacheInterface
    {
        try {
            $redis = new \Redis();
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
            error_log('Redis cache creation failed: ' . $e->getMessage());

            throw $e;
        }
    }

    private static function createAutoCache(): CacheInterface
    {
        // Try Redis first, fallback to in-memory
        try {
            // Check if Redis extension is available
            if (extension_loaded('redis')) {
                $redis = new \Redis();
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
        } catch (\Exception $e) {
            error_log('Redis cache not available, falling back to in-memory cache: ' . $e->getMessage());
        }

        // Fallback to in-memory cache
        return new InMemoryCache();
    }
}
