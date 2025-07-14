<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, ?int $ttl = null): bool;

    public function delete(string $key): bool;

    public function clear(): bool;

    public function has(string $key): bool;

    // Advanced features
    /**
     * @param array<string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMultiple(array $keys): array;

    /**
     * @param array<string, mixed> $values
     */
    public function setMultiple(array $values, ?int $ttl = null): bool;

    /**
     * @param array<string> $keys
     */
    public function deleteMultiple(array $keys): bool;

    public function invalidateByTag(string $tag): bool;

    /**
     * @param array<string> $tags
     */
    public function invalidateByTags(array $tags): bool;

    public function tag(string $key, string $tag): bool;

    /**
     * @param array<string> $tags
     */
    public function tagMultiple(string $key, array $tags): bool;

    /**
     * @return array<string, mixed>
     */
    public function getStats(): array;

    public function getHitRate(): float;

    public function getMissRate(): float;

    public function getTotalRequests(): int;

    public function getTotalHits(): int;

    public function getTotalMisses(): int;

    public function resetStats(): void;

    public function calculateOptimalTtl(string $key, int $baseTtl = 3600): int;

    public function setWithSmartTtl(string $key, mixed $value, int $baseTtl = 3600): bool;
}
