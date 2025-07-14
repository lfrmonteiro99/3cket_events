<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

class InMemoryCache implements CacheInterface
{
    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, int> */
    private array $expirations = [];

    /** @var array<string, array<string, bool>> */
    private array $tags = [];

    /** @var array<string, array<string, bool>> */
    private array $taggedKeys = [];

    /** @var array<string, int> */
    private array $stats = [
        'requests' => 0,
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
    ];

    /** @var array<string, array{hits: int, misses: int, last_access: int}> */
    private array $accessPatterns = [];

    /** @var array<string, array<int, array{ttl: int, timestamp: int}>> */
    private array $ttlHistory = [];

    public function get(string $key): mixed
    {
        $this->stats['requests']++;

        if (!$this->has($key)) {
            $this->stats['misses']++;
            $this->recordAccessPattern($key, false);

            return null;
        }

        $this->stats['hits']++;
        $this->recordAccessPattern($key, true);

        return $this->data[$key];
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->data[$key] = $value;
        $this->stats['sets']++;

        if ($ttl !== null && $ttl > 0) {
            $this->expirations[$key] = time() + $ttl;
        } else {
            unset($this->expirations[$key]);
        }

        $this->recordTtlHistory($key, $ttl ?? 0);

        return true;
    }

    public function delete(string $key): bool
    {
        $this->stats['deletes']++;
        unset($this->data[$key], $this->expirations[$key], $this->accessPatterns[$key]);

        // Remove from tags
        if (isset($this->taggedKeys[$key])) {
            foreach ($this->taggedKeys[$key] as $tag => $value) {
                unset($this->tags[$tag][$key]);
            }
            unset($this->taggedKeys[$key]);
        }

        return true;
    }

    public function clear(): bool
    {
        $this->data = [];
        $this->expirations = [];
        $this->tags = [];
        $this->taggedKeys = [];
        $this->accessPatterns = [];
        $this->ttlHistory = [];
        $this->resetStats();

        return true;
    }

    public function has(string $key): bool
    {
        if (!isset($this->data[$key])) {
            return false;
        }

        if (isset($this->expirations[$key]) && $this->expirations[$key] < time()) {
            $this->delete($key);

            return false;
        }

        return true;
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMultiple(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMultiple(array $values, ?int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param array<string> $keys
     */
    public function deleteMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function invalidateByTag(string $tag): bool
    {
        if (!isset($this->tags[$tag])) {
            return true;
        }

        $keysToDelete = array_keys($this->tags[$tag]);

        foreach ($keysToDelete as $key) {
            $this->delete($key);
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

        $this->tags[$tag][$key] = true;
        $this->taggedKeys[$key][$tag] = true;

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
        return [
            'requests' => $this->stats['requests'],
            'hits' => $this->stats['hits'],
            'misses' => $this->stats['misses'],
            'sets' => $this->stats['sets'],
            'deletes' => $this->stats['deletes'],
            'hit_rate' => $this->getHitRate(),
            'miss_rate' => $this->getMissRate(),
            'total_keys' => count($this->data),
            'tagged_keys' => count($this->taggedKeys),
            'total_tags' => count($this->tags),
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
        $accessCount = $this->accessPatterns[$key]['hits'] ?? 0;
        $missCount = $this->accessPatterns[$key]['misses'] ?? 0;
        $totalAccess = $accessCount + $missCount;

        if ($totalAccess === 0) {
            return $baseTtl;
        }

        $hitRate = $accessCount / $totalAccess;

        // Adjust TTL based on hit rate
        if ($hitRate > 0.8) {
            // High hit rate - increase TTL
            return (int) ($baseTtl * 1.5);
        }

        if ($hitRate < 0.2) {
            // Low hit rate - decrease TTL
            return (int) ($baseTtl * 0.5);
        }

        return $baseTtl;
    }

    public function setWithSmartTtl(string $key, mixed $value, int $baseTtl = 3600): bool
    {
        $optimalTtl = $this->calculateOptimalTtl($key, $baseTtl);

        return $this->set($key, $value, $optimalTtl);
    }

    private function recordAccessPattern(string $key, bool $isHit): void
    {
        if (!isset($this->accessPatterns[$key])) {
            $this->accessPatterns[$key] = ['hits' => 0, 'misses' => 0, 'last_access' => time()];
        }

        if ($isHit) {
            $this->accessPatterns[$key]['hits']++;
        } else {
            $this->accessPatterns[$key]['misses']++;
        }

        $this->accessPatterns[$key]['last_access'] = time();
    }

    private function recordTtlHistory(string $key, int $ttl): void
    {
        if (!isset($this->ttlHistory[$key])) {
            $this->ttlHistory[$key] = [];
        }

        $this->ttlHistory[$key][] = [
            'ttl' => $ttl,
            'timestamp' => time(),
        ];

        // Keep only last 10 TTL records
        if (count($this->ttlHistory[$key]) > 10) {
            array_shift($this->ttlHistory[$key]);
        }
    }
}
