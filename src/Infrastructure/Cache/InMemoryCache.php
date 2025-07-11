<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

class InMemoryCache implements CacheInterface
{
    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, int> */
    private array $expiry = [];

    public function get(string $key): mixed
    {
        $this->cleanupExpired();

        if (!$this->exists($key)) {
            return null;
        }

        return $this->data[$key];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $this->data[$key] = $value;

        if ($ttl > 0) {
            $this->expiry[$key] = time() + $ttl;
        } else {
            // Remove expiry if TTL is 0 (no expiration)
            unset($this->expiry[$key]);
        }

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->data[$key], $this->expiry[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->data = [];
        $this->expiry = [];

        return true;
    }

    public function exists(string $key): bool
    {
        if (!isset($this->data[$key])) {
            return false;
        }

        // Check if expired
        if (isset($this->expiry[$key]) && $this->expiry[$key] <= time()) {
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
        $this->cleanupExpired();

        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * Get cache statistics for monitoring.
     */
    /**
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        $this->cleanupExpired();

        return [
            'total_items' => count($this->data),
            'expired_items' => count($this->expiry),
            'memory_usage' => memory_get_usage(),
            'cache_hits' => 0, // Could be implemented with counters
        ];
    }

    /**
     * Remove expired items from cache.
     */
    private function cleanupExpired(): void
    {
        $currentTime = time();

        foreach ($this->expiry as $key => $expiryTime) {
            if ($expiryTime <= $currentTime) {
                $this->delete($key);
            }
        }
    }
}
