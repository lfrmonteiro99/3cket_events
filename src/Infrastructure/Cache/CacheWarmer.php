<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;

class CacheWarmer
{
    private CacheInterface $cache;
    private EventRepositoryInterface $eventRepository;
    private LoggerInterface $logger;

    public function __construct(
        CacheInterface $cache,
        EventRepositoryInterface $eventRepository,
        LoggerInterface $logger
    ) {
        $this->cache = $cache;
        $this->eventRepository = $eventRepository;
        $this->logger = $logger;
    }

    public function warmUp(): void
    {
        $this->logger->info('Starting cache warm-up process');

        try {
            $this->warmUpPopularEvents();
            $this->warmUpEventList();
            $this->warmUpSearchIndexes();

            $this->logger->info('Cache warm-up completed successfully');
        } catch (\Exception $e) {
            $this->logger->error('Cache warm-up failed: ' . $e->getMessage());
        }
    }

    public function warmUpSpecificEvent(string $eventId): void
    {
        $this->logger->info("Warming up specific event: {$eventId}");

        try {
            $event = $this->eventRepository->findById(new \App\Domain\ValueObject\EventId((int) $eventId));

            if ($event) {
                $key = "event:{$eventId}";
                $this->cache->setWithSmartTtl($key, $event, 3600);
                $this->cache->tagMultiple($key, ['events', 'specific']);
                $this->logger->info("Successfully warmed up event: {$eventId}");
            }
        } catch (\Exception $e) {
            $this->logger->error("Failed to warm up event {$eventId}: " . $e->getMessage());
        }
    }

    public function warmUpByTag(string $tag): void
    {
        $this->logger->info("Warming up cache for tag: {$tag}");

        // This would typically invalidate and re-warm specific tagged content
        $this->cache->invalidateByTag($tag);

        // Re-warm based on tag type
        switch ($tag) {
            case 'events':
                $this->warmUpPopularEvents();
                break;
            case 'list':
                $this->warmUpEventList();
                break;
            case 'search':
                $this->warmUpSearchIndexes();
                break;
            default:
                $this->logger->info("No specific warm-up strategy for tag: {$tag}");
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getWarmUpStats(): array
    {
        $stats = $this->cache->getStats();

        return [
            'cache_stats' => $stats,
            'warm_up_status' => 'completed',
            'last_warm_up' => date('Y-m-d H:i:s'),
        ];
    }

    private function warmUpPopularEvents(): void
    {
        $this->logger->info('Warming up popular events cache');

        // Warm up first 10 events (most likely to be accessed)
        $events = $this->eventRepository->findAll();
        $firstTen = array_slice($events, 0, 10);

        foreach ($firstTen as $event) {
            $eventId = $event->getId();

            if ($eventId !== null) {
                $key = "event:{$eventId->getValue()}";
                $this->cache->setWithSmartTtl($key, $event, 3600);
                $this->cache->tagMultiple($key, ['events', 'popular']);
            }
        }

        $this->logger->info('Warmed up ' . count($firstTen) . ' popular events');
    }

    private function warmUpEventList(): void
    {
        $this->logger->info('Warming up event list cache');

        // Warm up paginated results for common page sizes
        $pageSizes = [10, 20, 50];
        $pageNumbers = [1, 2, 3];

        foreach ($pageSizes as $pageSize) {
            foreach ($pageNumbers as $page) {
                $offset = ($page - 1) * $pageSize;
                $events = array_slice($this->eventRepository->findAll(), $offset, $pageSize);
                $total = $this->eventRepository->count();

                $key = "events:list:{$pageSize}:{$page}";
                $data = [
                    'events' => $events,
                    'total' => $total,
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'totalPages' => ceil($total / $pageSize),
                ];

                $this->cache->setWithSmartTtl($key, $data, 1800); // 30 minutes
                $this->cache->tagMultiple($key, ['events', 'list', "page_size_{$pageSize}"]);
            }
        }

        $this->logger->info('Warmed up event list cache for multiple page sizes');
    }

    private function warmUpSearchIndexes(): void
    {
        $this->logger->info('Warming up search indexes cache');

        // Warm up common search terms
        $commonSearches = [
            'concert' => new \App\Application\Query\SearchQuery(search: 'concert'),
            'festival' => new \App\Application\Query\SearchQuery(search: 'festival'),
            'sports' => new \App\Application\Query\SearchQuery(search: 'sports'),
            'theater' => new \App\Application\Query\SearchQuery(search: 'theater'),
            'conference' => new \App\Application\Query\SearchQuery(search: 'conference'),
        ];

        foreach ($commonSearches as $searchKey => $searchQuery) {
            try {
                $results = $this->eventRepository->search($searchQuery);
                $total = $this->eventRepository->countSearch($searchQuery);

                $key = "search:{$searchKey}";
                $data = [
                    'results' => $results,
                    'total' => $total,
                    'search_params' => $searchQuery,
                ];

                $this->cache->setWithSmartTtl($key, $data, 900); // 15 minutes
                $this->cache->tagMultiple($key, ['search', 'index', 'common']);

            } catch (\Exception $e) {
                $this->logger->warning("Failed to warm up search index for '{$searchKey}': " . $e->getMessage());
            }
        }

        $this->logger->info('Warmed up search indexes cache');
    }
}
