<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Query\PaginationQuery;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;
use App\Infrastructure\Cache\CacheInterface;

class CachedEventRepository implements EventRepositoryInterface
{
    private EventRepositoryInterface $repository;
    private CacheInterface $cache;
    private int $defaultTtl;
    private string $keyPrefix;

    public function __construct(
        EventRepositoryInterface $repository,
        CacheInterface $cache,
        int $defaultTtl = 3600,
        string $keyPrefix = 'events:'
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->defaultTtl = $defaultTtl;
        $this->keyPrefix = $keyPrefix;
    }

    public function findAll(): array
    {
        $cacheKey = $this->keyPrefix . 'all';

        // Try to get from cache first
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            error_log('CachedEventRepository: Cache HIT for findAll()');

            return $cached;
        }

        // Cache miss - get from repository
        error_log('CachedEventRepository: Cache MISS for findAll() - fetching from repository');
        $events = $this->repository->findAll();

        // Store in cache
        $this->cache->set($cacheKey, $events, $this->defaultTtl);

        return $events;
    }

    public function findPaginated(PaginationQuery $query): array
    {
        $cacheKey = $this->keyPrefix . 'paginated:' . $query->getCacheKey();

        // Try to get from cache first
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            error_log("CachedEventRepository: Cache HIT for findPaginated({$query->getCacheKey()})");

            return $cached;
        }

        // Cache miss - get from repository
        error_log("CachedEventRepository: Cache MISS for findPaginated({$query->getCacheKey()}) - fetching from repository");
        $events = $this->repository->findPaginated($query);

        // Store in cache with shorter TTL since paginated results can change frequently
        $this->cache->set($cacheKey, $events, $this->defaultTtl / 2);

        return $events;
    }

    public function findById(EventId $id): ?Event
    {
        $idValue = $id->getValue();
        $cacheKey = $this->keyPrefix . 'id:' . $idValue;

        // Try to get from cache first
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            error_log("CachedEventRepository: Cache HIT for findById({$idValue})");

            return $cached;
        }

        // Cache miss - get from repository
        error_log("CachedEventRepository: Cache MISS for findById({$idValue}) - fetching from repository");
        $event = $this->repository->findById($id);

        // Store in cache (even if null to prevent repeated lookups)
        $this->cache->set($cacheKey, $event, $this->defaultTtl);

        return $event;
    }

    public function count(): int
    {
        $cacheKey = $this->keyPrefix . 'count';

        // Try to get from cache first
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            error_log('CachedEventRepository: Cache HIT for count()');

            return $cached;
        }

        // Cache miss - get from repository
        error_log('CachedEventRepository: Cache MISS for count() - fetching from repository');
        $count = $this->repository->count();

        // Store in cache with shorter TTL since count changes more frequently
        $this->cache->set($cacheKey, $count, $this->defaultTtl / 2);

        return $count;
    }

    public function save(Event $event): Event
    {
        // Save the event in the repository
        $savedEvent = $this->repository->save($event);

        // Invalidate related cache entries since data has changed
        $this->invalidateListCaches();

        // Cache the saved event
        if ($savedEvent->getId() !== null) {
            $cacheKey = $this->keyPrefix . 'id:' . $savedEvent->getId()->getValue();
            $this->cache->set($cacheKey, $savedEvent, $this->defaultTtl);
        }

        error_log('CachedEventRepository: Saved event and invalidated list caches');

        return $savedEvent;
    }

    public function delete(EventId $id): bool
    {
        // Delete from repository
        $result = $this->repository->delete($id);

        if ($result) {
            // Invalidate related cache entries
            $this->invalidateListCaches();
            $this->cache->delete($this->keyPrefix . 'id:' . $id->getValue());

            error_log("CachedEventRepository: Deleted event {$id->getValue()} and invalidated caches");
        }

        return $result;
    }

    public function nextId(): EventId
    {
        return $this->repository->nextId();
    }

    /**
     * Clear all cache entries for this repository.
     */
    public function clearCache(): bool
    {
        // Since we can't easily get all keys with a prefix in all cache implementations,
        // we'll clear the main cache entries we know about
        $keys = [
            $this->keyPrefix . 'all',
            $this->keyPrefix . 'count',
        ];

        $success = true;

        foreach ($keys as $key) {
            if (!$this->cache->delete($key)) {
                $success = false;
            }
        }

        error_log('CachedEventRepository: Cleared all cache entries');

        return $success;
    }

    /**
     * Get cache statistics.
     */
    /**
     * @return array<string, mixed>
     */
    public function getCacheStats(): array
    {
        if (method_exists($this->cache, 'getStats')) {
            /** @var array<string, mixed> $stats */
            $stats = call_user_func([$this->cache, 'getStats']);
            return $stats;
        }

        return ['message' => 'Cache statistics not available'];
    }

    /**
     * Invalidate list-related cache entries (all, count, paginated).
     */
    private function invalidateListCaches(): void
    {
        $this->cache->delete($this->keyPrefix . 'all');
        $this->cache->delete($this->keyPrefix . 'count');
        
        // Clear paginated cache entries - we can't easily clear all keys with a prefix
        // in all cache implementations, so we'll clear the main cache during save/delete
        // This is a limitation that could be improved with a more sophisticated cache implementation
        $this->cache->clear();
    }
}
