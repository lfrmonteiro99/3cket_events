<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Redis;

class RedisCache implements CacheInterface
{
    private Redis $redis;

    /** @var array<string, int> */
    private array $stats = [
        'requests' => 0,
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
    ];

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key): mixed
    {
        $this->stats['requests']++;

        $value = $this->redis->get($key);

        if ($value === false) {
            $this->stats['misses']++;

            return null;
        }

        $this->stats['hits']++;

        return unserialize($value);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->stats['sets']++;

        $serializedValue = serialize($value);

        if ($ttl !== null && $ttl > 0) {
            return $this->redis->setex($key, $ttl, $serializedValue);
        }

        return $this->redis->set($key, $serializedValue);
    }

    public function delete(string $key): bool
    {
        $this->stats['deletes']++;

        // Remove from tags
        $this->removeFromTags($key);

        $result = $this->redis->del($key);

        return is_int($result) && $result > 0;
    }

    public function clear(): bool
    {
        $this->resetStats();

        return $this->redis->flushDB();
    }

    public function has(string $key): bool
    {
        $result = $this->redis->exists($key);

        return is_int($result) && $result > 0;
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMultiple(array $keys): array
    {
        if (empty($keys)) {
            return [];
        }

        $values = $this->redis->mget($keys);
        $result = [];

        foreach ($keys as $index => $key) {
            $value = $values[$index] ?? false;
            $result[$key] = $value === false ? null : unserialize($value);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMultiple(array $values, ?int $ttl = null): bool
    {
        if (empty($values)) {
            return true;
        }

        $pipeline = $this->redis->multi();

        foreach ($values as $key => $value) {
            $serializedValue = serialize($value);

            if ($ttl !== null && $ttl > 0) {
                $pipeline->setex($key, $ttl, $serializedValue);
            } else {
                $pipeline->set($key, $serializedValue);
            }
        }

        $pipeline->exec();

        return true;
    }

    /**
     * @param array<string> $keys
     */
    public function deleteMultiple(array $keys): bool
    {
        if (empty($keys)) {
            return true;
        }

        // Remove from tags
        foreach ($keys as $key) {
            $this->removeFromTags($key);
        }

        $result = $this->redis->del(...$keys);

        return is_int($result) && $result > 0;
    }

    public function invalidateByTag(string $tag): bool
    {
        $tagKey = "tag:{$tag}";
        $keys = $this->redis->smembers($tagKey);

        if (!empty($keys)) {
            $this->redis->del(...$keys);
            $this->redis->del($tagKey);
        }

        return true;
    }

    /**
     * @param array<string> $tags
     */
    public function invalidateByTags(array $tags): bool
    {
        foreach ($tags as $tag) {
            $this->invalidateByTag($tag);
        }

        return true;
    }

    public function tag(string $key, string $tag): bool
    {
        if (!$this->has($key)) {
            return false;
        }

        $tagKey = "tag:{$tag}";
        $this->redis->sadd($tagKey, $key);
        $this->redis->setex("key_tag:{$key}:{$tag}", 86400, '1'); // 24h TTL for tag association

        return true;
    }

    /**
     * @param array<string> $tags
     */
    public function tagMultiple(string $key, array $tags): bool
    {
        foreach ($tags as $tag) {
            $this->tag($key, $tag);
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        $info = $this->redis->info();

        return [
            'requests' => $this->stats['requests'],
            'hits' => $this->stats['hits'],
            'misses' => $this->stats['misses'],
            'sets' => $this->stats['sets'],
            'deletes' => $this->stats['deletes'],
            'hit_rate' => $this->getHitRate(),
            'miss_rate' => $this->getMissRate(),
            'redis_used_memory' => $info['used_memory'] ?? 0,
            'redis_used_memory_peak' => $info['used_memory_peak'] ?? 0,
            'redis_connected_clients' => $info['connected_clients'] ?? 0,
            'redis_total_commands_processed' => $info['total_commands_processed'] ?? 0,
        ];
    }

    public function getHitRate(): float
    {
        if ($this->stats['requests'] === 0) {
            return 0.0;
        }

        return round(($this->stats['hits'] / $this->stats['requests']) * 100, 2);
    }

    public function getMissRate(): float
    {
        if ($this->stats['requests'] === 0) {
            return 0.0;
        }

        return round(($this->stats['misses'] / $this->stats['requests']) * 100, 2);
    }

    public function getTotalRequests(): int
    {
        return $this->stats['requests'];
    }

    public function getTotalHits(): int
    {
        return $this->stats['hits'];
    }

    public function getTotalMisses(): int
    {
        return $this->stats['misses'];
    }

    public function resetStats(): void
    {
        $this->stats = [
            'requests' => 0,
            'hits' => 0,
            'misses' => 0,
            'sets' => 0,
            'deletes' => 0,
        ];
    }

    public function calculateOptimalTtl(string $key, int $baseTtl = 3600): int
    {
        // Get access pattern from Redis
        $accessKey = "access:{$key}";
        $accessData = $this->redis->get($accessKey);

        if ($accessData === false) {
            return $baseTtl;
        }

        $access = json_decode($accessData, true);
        $hits = $access['hits'] ?? 0;
        $misses = $access['misses'] ?? 0;
        $totalAccess = $hits + $misses;

        if ($totalAccess === 0) {
            return $baseTtl;
        }

        $hitRate = $hits / $totalAccess;

        // Adjust TTL based on hit rate
        if ($hitRate > 0.8) {
            return (int) ($baseTtl * 1.5);
        }

        if ($hitRate < 0.2) {
            return (int) ($baseTtl * 0.5);
        }

        return $baseTtl;
    }

    public function setWithSmartTtl(string $key, mixed $value, int $baseTtl = 3600): bool
    {
        $optimalTtl = $this->calculateOptimalTtl($key, $baseTtl);

        return $this->set($key, $value, $optimalTtl);
    }

    private function removeFromTags(string $key): void
    {
        // Find all tags for this key
        $pattern = "key_tag:{$key}:*";
        $tagKeys = $this->redis->keys($pattern);

        foreach ($tagKeys as $tagKey) {
            $parts = explode(':', $tagKey);
            $tag = end($parts);
            $tagSetKey = "tag:{$tag}";

            $this->redis->srem($tagSetKey, $key);
            $this->redis->del($tagKey);
        }
    }
}
