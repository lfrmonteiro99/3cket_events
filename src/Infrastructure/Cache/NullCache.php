<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

class NullCache implements CacheInterface
{
    /** @var array<string, int> */
    private array $stats = [
        'requests' => 0,
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0,
    ];

    public function get(string $key): mixed
    {
        $this->stats['requests']++;
        $this->stats['misses']++;

        return null;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->stats['sets']++;

        return true;
    }

    public function delete(string $key): bool
    {
        $this->stats['deletes']++;

        return true;
    }

    public function clear(): bool
    {
        $this->resetStats();

        return true;
    }

    public function has(string $key): bool
    {
        return false;
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
            $result[$key] = null;
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
        return true;
    }

    /**
     * @param array<string> $tags
     */
    public function invalidateByTags(array $tags): bool
    {
        return true;
    }

    public function tag(string $key, string $tag): bool
    {
        return true;
    }

    /**
     * @param array<string> $tags
     */
    public function tagMultiple(string $key, array $tags): bool
    {
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
            'total_keys' => 0,
            'tagged_keys' => 0,
            'total_tags' => 0,
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
        return $baseTtl;
    }

    public function setWithSmartTtl(string $key, mixed $value, int $baseTtl = 3600): bool
    {
        return $this->set($key, $value, $baseTtl);
    }
}
