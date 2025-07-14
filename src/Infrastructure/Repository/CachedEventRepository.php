<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Query\PaginationQuery;
use App\Application\Query\SearchQuery;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;
use App\Infrastructure\Cache\CacheAnalytics;
use App\Infrastructure\Cache\CacheInterface;

class CachedEventRepository implements EventRepositoryInterface
{
    private EventRepositoryInterface $repository;
    private CacheInterface $cache;
    private CacheAnalytics $analytics;
    private int $defaultTtl;

    public function __construct(
        EventRepositoryInterface $repository,
        CacheInterface $cache,
        CacheAnalytics $analytics,
        int $defaultTtl = 3600
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->analytics = $analytics;
        $this->defaultTtl = $defaultTtl;
    }

    public function findAll(): array
    {
        $key = 'events:all';
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $events = $this->repository->findAll();

        if (!empty($events)) {
            $this->cache->setWithSmartTtl($key, $events, $this->defaultTtl);
            $this->cache->tagMultiple($key, ['events', 'list', 'all_events']);
        }

        return $events;
    }

    public function findPaginated(PaginationQuery $query): array
    {
        $key = 'events:paginated:' . $query->getCacheKey();
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $events = $this->repository->findPaginated($query);

        if (!empty($events)) {
            $this->cache->setWithSmartTtl($key, $events, $this->defaultTtl);
            $this->cache->tagMultiple($key, ['events', 'list', 'paginated']);
        }

        return $events;
    }

    public function findById(EventId $id): ?Event
    {
        $key = "event:{$id->getValue()}";
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $event = $this->repository->findById($id);

        if ($event) {
            $this->cache->setWithSmartTtl($key, $event, $this->defaultTtl);
            $this->cache->tagMultiple($key, ['events', 'specific', 'event_id']);
        }

        return $event;
    }

    public function count(): int
    {
        $key = 'events:count';
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $count = $this->repository->count();

        $this->cache->setWithSmartTtl($key, $count, $this->defaultTtl * 2); // Longer TTL for count
        $this->cache->tagMultiple($key, ['events', 'count', 'metadata']);

        return $count;
    }

    public function search(SearchQuery $query): array
    {
        $key = 'events:search:' . $query->getCacheKey();
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $events = $this->repository->search($query);

        if (!empty($events)) {
            $this->cache->setWithSmartTtl($key, $events, $this->defaultTtl);
            $this->cache->tagMultiple($key, ['events', 'search', 'search_results']);
        }

        return $events;
    }

    public function countSearch(SearchQuery $query): int
    {
        $key = 'events:search_count:' . $query->getCacheKey();
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->analytics->trackKeyAccess($key, true);

            return $cached;
        }

        $this->analytics->trackKeyAccess($key, false);
        $count = $this->repository->countSearch($query);

        $this->cache->setWithSmartTtl($key, $count, $this->defaultTtl * 2); // Longer TTL for count
        $this->cache->tagMultiple($key, ['events', 'search', 'count', 'metadata']);

        return $count;
    }

    public function invalidateEventCache(string $eventId): void
    {
        $keys = [
            "event:{$eventId}",
            'events:count',
        ];

        $this->cache->deleteMultiple($keys);
        $this->cache->invalidateByTags(['events', 'specific']);
    }

    public function invalidateSearchCache(): void
    {
        $this->cache->invalidateByTags(['search', 'search_results']);
    }

    public function invalidateAllCache(): void
    {
        $this->cache->invalidateByTags(['events', 'list', 'search', 'count']);
    }

    /**
     * @return array<string, mixed>
     */
    public function getCacheStats(): array
    {
        return $this->cache->getStats();
    }

    /**
     * @return array<string, mixed>
     */
    public function getCacheAnalytics(): array
    {
        return $this->analytics->generateReport();
    }

    public function warmUpCache(): void
    {
        // Warm up popular events
        $popularEvents = $this->repository->findAll();
        $firstTen = array_slice($popularEvents, 0, 10);

        foreach ($firstTen as $event) {
            $eventId = $event->getId();

            if ($eventId !== null) {
                $key = "event:{$eventId->getValue()}";
                $this->cache->setWithSmartTtl($key, $event, $this->defaultTtl);
                $this->cache->tagMultiple($key, ['events', 'popular', 'warmed']);
            }
        }

        // Warm up count
        $this->count();

        // Warm up common searches
        $commonSearches = [
            new SearchQuery(search: 'concert'),
            new SearchQuery(search: 'festival'),
            new SearchQuery(search: 'sports'),
        ];

        foreach ($commonSearches as $search) {
            $this->search($search);
            $this->countSearch($search);
        }
    }
}
