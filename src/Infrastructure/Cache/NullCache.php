<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

class NullCache implements CacheInterface
{
    public function get(string $key): mixed
    {
        return null;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return true; // Always return true but don't actually cache
    }

    public function delete(string $key): bool
    {
        return true;
    }

    public function clear(): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return false; // Never has cached items
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
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        return true; // Always return true but don't actually cache
    }

    /**
     * Get cache statistics - always empty for null cache.
     */
    /**
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        return [
            'driver' => 'null',
            'total_items' => 0,
            'memory_usage' => 0,
            'message' => 'Caching is disabled',
        ];
    }
}
