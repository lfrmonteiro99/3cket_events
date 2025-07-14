<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Repository\CachedEventRepository;

class CacheManager
{
    public function __construct(
        private readonly CachedEventRepository $repository,
        private readonly CacheWarmer $warmer,
        private readonly CacheAnalytics $analytics,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        return $this->repository->getCacheStats();
    }

    /**
     * @return array<string, mixed>
     */
    public function getAnalytics(): array
    {
        $this->logger->info('Generating cache analytics report');

        return $this->analytics->generateReport();
    }

    public function warmUp(): void
    {
        $this->warmer->warmUp();
    }

    public function invalidateAll(): void
    {
        $this->repository->invalidateAllCache();
    }

    public function invalidateEvent(string $eventId): void
    {
        $this->repository->invalidateEventCache($eventId);
    }

    public function invalidateSearch(): void
    {
        $this->repository->invalidateSearchCache();
    }
}
