<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

interface CacheInterface
{
    /**
     * Retrieve an item from the cache.
     *
     * @param string $key
     *
     * @return mixed Returns null if key doesn't exist
     */
    public function get(string $key): mixed;

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl   Time to live in seconds (0 = no expiration)
     *
     * @return bool True on success
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     *
     * @return bool True if item was removed or didn't exist
     */
    public function delete(string $key): bool;

    /**
     * Clear all items from the cache.
     *
     * @return bool True on success
     */
    public function clear(): bool;

    /**
     * Check if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Get multiple items from the cache.
     *
     * @param array<string> $keys
     *
     * @return array<string, mixed> Key-value pairs (missing keys will have null values)
     */
    public function getMultiple(array $keys): array;

    /**
     * Set multiple items in the cache.
     *
     * @param array<string, mixed> $values Key-value pairs
     * @param int                  $ttl    Time to live in seconds
     *
     * @return bool True on success
     */
    public function setMultiple(array $values, int $ttl = 3600): bool;
}
