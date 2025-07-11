<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Redis;
use RedisException;

class RedisCache implements CacheInterface
{
    private Redis $redis;
    private string $prefix;

    public function __construct(Redis $redis, string $prefix = '3cket:')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function get(string $key): mixed
    {
        try {
            $value = $this->redis->get($this->prefixKey($key));

            if ($value === false) {
                return null;
            }

            return unserialize($value);
        } catch (RedisException $e) {
            error_log("Redis get error for key {$key}: " . $e->getMessage());

            return null;
        }
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        try {
            $serialized = serialize($value);

            if ($ttl > 0) {
                return $this->redis->setex($this->prefixKey($key), $ttl, $serialized);
            }

            return $this->redis->set($this->prefixKey($key), $serialized);

        } catch (RedisException $e) {
            error_log("Redis set error for key {$key}: " . $e->getMessage());

            return false;
        }
    }

    public function delete(string $key): bool
    {
        try {
            $result = $this->redis->del($this->prefixKey($key));

            return is_int($result) && $result >= 0; // Redis returns number of keys deleted
        } catch (RedisException $e) {
            error_log("Redis delete error for key {$key}: " . $e->getMessage());

            return false;
        }
    }

    public function clear(): bool
    {
        try {
            $keys = $this->redis->keys($this->prefix . '*');

            if (!empty($keys)) {
                $result = $this->redis->del($keys);

                return is_int($result) && $result > 0;
            }

            return true;
        } catch (RedisException $e) {
            error_log('Redis clear error: ' . $e->getMessage());

            return false;
        }
    }

    public function exists(string $key): bool
    {
        try {
            $result = $this->redis->exists($this->prefixKey($key));

            return is_int($result) && $result > 0;
        } catch (RedisException $e) {
            error_log("Redis exists error for key {$key}: " . $e->getMessage());

            return false;
        }
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMultiple(array $keys): array
    {
        try {
            $prefixedKeys = array_map([$this, 'prefixKey'], $keys);
            $values = $this->redis->mget($prefixedKeys);

            $result = [];

            foreach ($keys as $index => $key) {
                $value = $values[$index];
                $result[$key] = $value !== false ? unserialize($value) : null;
            }

            return $result;
        } catch (RedisException $e) {
            error_log('Redis getMultiple error: ' . $e->getMessage());

            // Fallback to individual gets
            $result = [];

            foreach ($keys as $key) {
                $result[$key] = $this->get($key);
            }

            return $result;
        }
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        try {
            $pipe = $this->redis->pipeline();

            foreach ($values as $key => $value) {
                $serialized = serialize($value);

                if ($ttl > 0) {
                    $pipe->setex($this->prefixKey($key), $ttl, $serialized);
                } else {
                    $pipe->set($this->prefixKey($key), $serialized);
                }
            }

            $results = $pipe->exec();

            return !in_array(false, $results, true);

        } catch (RedisException $e) {
            error_log('Redis setMultiple error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get Redis connection statistics.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        try {
            $info = $this->redis->info();

            return [
                'redis_version' => $info['redis_version'] ?? 'unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory' => $info['used_memory'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? '0',
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
            ];
        } catch (RedisException $e) {
            error_log('Redis stats error: ' . $e->getMessage());

            return [];
        }
    }

    public static function createFromEnvironment(): self
    {
        $redis = new Redis();

        $host = $_ENV['REDIS_HOST'] ?? 'redis';
        $port = (int) ($_ENV['REDIS_PORT'] ?? 6379);
        $password = $_ENV['REDIS_PASSWORD'] ?? null;
        $database = (int) ($_ENV['REDIS_DATABASE'] ?? 0);

        $redis->connect($host, $port, 2.5); // 2.5 second timeout

        if ($password) {
            $redis->auth($password);
        }

        if ($database > 0) {
            $redis->select($database);
        }

        return new self($redis);
    }

    private function prefixKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
