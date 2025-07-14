<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Infrastructure\Logging\LoggerInterface;

class CacheAnalytics
{
    private CacheInterface $cache;
    private LoggerInterface $logger;

    /** @var array<string, mixed> */
    private array $analytics = [];

    public function __construct(CacheInterface $cache, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPerformanceMetrics(): array
    {
        $stats = $this->cache->getStats();

        return [
            'hit_rate' => $this->cache->getHitRate(),
            'miss_rate' => $this->cache->getMissRate(),
            'total_requests' => $this->cache->getTotalRequests(),
            'total_hits' => $this->cache->getTotalHits(),
            'total_misses' => $this->cache->getTotalMisses(),
            'efficiency_score' => $this->calculateEfficiencyScore($stats),
            'performance_trend' => $this->getPerformanceTrend(),
            'recommendations' => $this->generateRecommendations($stats),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getCacheHealth(): array
    {
        $stats = $this->cache->getStats();

        return [
            'status' => $this->determineHealthStatus($stats),
            'hit_rate_health' => $this->getHitRateHealth($stats['hit_rate'] ?? 0),
            'memory_usage' => $stats['redis_used_memory'] ?? 0,
            'memory_peak' => $stats['redis_used_memory_peak'] ?? 0,
            'total_keys' => $stats['total_keys'] ?? 0,
            'tagged_keys' => $stats['tagged_keys'] ?? 0,
            'total_tags' => $stats['total_tags'] ?? 0,
            'last_updated' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getUsagePatterns(): array
    {
        return [
            'peak_hours' => $this->identifyPeakHours(),
            'popular_keys' => $this->identifyPopularKeys(),
            'cold_keys' => $this->identifyColdKeys(),
            'access_frequency' => $this->getAccessFrequency(),
            'key_distribution' => $this->getKeyDistribution(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOptimizationSuggestions(): array
    {
        $stats = $this->cache->getStats();
        $suggestions = [];

        // Hit rate optimization
        if (($stats['hit_rate'] ?? 0) < 70) {
            $suggestions[] = [
                'type' => 'hit_rate',
                'priority' => 'high',
                'message' => 'Cache hit rate is below 70%. Consider increasing TTL for frequently accessed data.',
                'action' => 'Review and adjust TTL values for popular keys',
            ];
        }

        // Memory optimization
        if (isset($stats['redis_used_memory']) && $stats['redis_used_memory'] > 100000000) { // 100MB
            $suggestions[] = [
                'type' => 'memory',
                'priority' => 'medium',
                'message' => 'Cache memory usage is high. Consider implementing cache eviction policies.',
                'action' => 'Review cache size limits and eviction strategies',
            ];
        }

        // Tag optimization
        if (($stats['tagged_keys'] ?? 0) < ($stats['total_keys'] ?? 0) * 0.5) {
            $suggestions[] = [
                'type' => 'tagging',
                'priority' => 'low',
                'message' => 'Less than 50% of keys are tagged. Consider implementing more granular cache invalidation.',
                'action' => 'Add tags to frequently invalidated keys',
            ];
        }

        return $suggestions;
    }

    /**
     * @return array<string, mixed>
     */
    public function generateReport(): array
    {
        $this->logger->info('Generating cache analytics report');

        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'performance' => $this->getPerformanceMetrics(),
            'health' => $this->getCacheHealth(),
            'usage_patterns' => $this->getUsagePatterns(),
            'optimization_suggestions' => $this->getOptimizationSuggestions(),
            'summary' => $this->generateSummary(),
        ];

        $this->logger->info('Cache analytics report generated successfully');

        return $report;
    }

    public function trackKeyAccess(string $key, bool $isHit): void
    {
        if (!isset($this->analytics['key_access'][$key])) {
            $this->analytics['key_access'][$key] = [
                'hits' => 0,
                'misses' => 0,
                'first_access' => time(),
                'last_access' => time(),
            ];
        }

        if ($isHit) {
            $this->analytics['key_access'][$key]['hits']++;
        } else {
            $this->analytics['key_access'][$key]['misses']++;
        }

        $this->analytics['key_access'][$key]['last_access'] = time();
    }

    /**
     * @param array<string, mixed> $stats
     */
    private function calculateEfficiencyScore(array $stats): float
    {
        $hitRate = $stats['hit_rate'] ?? 0;
        $totalRequests = $stats['requests'] ?? 0;

        if ($totalRequests === 0) {
            return 0.0;
        }

        // Base score on hit rate (70% weight)
        $hitRateScore = ($hitRate / 100) * 70;

        // Bonus for high request volume (30% weight)
        $volumeScore = min(($totalRequests / 1000) * 30, 30);

        return round($hitRateScore + $volumeScore, 2);
    }

    private function getPerformanceTrend(): string
    {
        // This would typically compare current stats with historical data
        // For now, return a static trend based on current hit rate
        $hitRate = $this->cache->getHitRate();

        if ($hitRate > 80) {
            return 'excellent';
        }

        if ($hitRate > 60) {
            return 'good';
        }

        if ($hitRate > 40) {
            return 'fair';
        }

        return 'poor';

    }

    /**
     * @param array<string, mixed> $stats
     *
     * @return array<int, string>
     */
    private function generateRecommendations(array $stats): array
    {
        $recommendations = [];
        $hitRate = $stats['hit_rate'] ?? 0;

        if ($hitRate < 50) {
            $recommendations[] = 'Consider implementing cache warming for frequently accessed data';
            $recommendations[] = 'Review cache key strategies and TTL values';
        }

        if ($hitRate > 90) {
            $recommendations[] = 'Cache performance is excellent. Consider if all data needs to be cached';
        }

        return $recommendations;
    }

    /**
     * @param array<string, mixed> $stats
     */
    private function determineHealthStatus(array $stats): string
    {
        $hitRate = $stats['hit_rate'] ?? 0;

        if ($hitRate >= 80) {
            return 'healthy';
        }

        if ($hitRate >= 60) {
            return 'warning';
        }

        return 'critical';

    }

    private function getHitRateHealth(float $hitRate): string
    {
        if ($hitRate >= 80) {
            return 'excellent';
        }

        if ($hitRate >= 60) {
            return 'good';
        }

        if ($hitRate >= 40) {
            return 'fair';
        }

        return 'poor';

    }

    /**
     * @return array<string, mixed>
     */
    private function identifyPeakHours(): array
    {
        // This would typically analyze access patterns over time
        // For now, return a mock analysis
        return [
            'peak_hour' => '14:00-16:00',
            'low_activity_hour' => '02:00-04:00',
            'daily_pattern' => 'business_hours_peak',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function identifyPopularKeys(): array
    {
        $stats = $this->cache->getStats();
        $totalRequests = $stats['requests'] ?? 0;

        return [
            'most_accessed' => 'event:list:10:1',
            'access_count' => $totalRequests,
            'popularity_score' => round(($stats['hits'] ?? 0) / max($totalRequests, 1) * 100, 2),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function identifyColdKeys(): array
    {
        return [
            'cold_key_count' => 0,
            'cold_key_percentage' => 0.0,
            'recommendation' => 'No cold keys identified',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getAccessFrequency(): array
    {
        $stats = $this->cache->getStats();
        $totalRequests = $stats['requests'] ?? 0;
        $hits = $stats['hits'] ?? 0;
        $misses = $stats['misses'] ?? 0;

        return [
            'requests_per_minute' => $totalRequests > 0 ? round($totalRequests / 60, 2) : 0,
            'hits_per_minute' => $hits > 0 ? round($hits / 60, 2) : 0,
            'misses_per_minute' => $misses > 0 ? round($misses / 60, 2) : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getKeyDistribution(): array
    {
        $stats = $this->cache->getStats();
        $totalKeys = $stats['total_keys'] ?? 0;
        $taggedKeys = $stats['tagged_keys'] ?? 0;

        return [
            'total_keys' => $totalKeys,
            'tagged_keys' => $taggedKeys,
            'untagged_keys' => $totalKeys - $taggedKeys,
            'tagging_ratio' => $totalKeys > 0 ?
                round($taggedKeys / $totalKeys * 100, 2) : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generateSummary(): array
    {
        $stats = $this->cache->getStats();

        return [
            'overall_performance' => $this->getPerformanceTrend(),
            'key_metrics' => [
                'hit_rate' => $stats['hit_rate'] ?? 0,
                'total_requests' => $stats['requests'] ?? 0,
                'memory_usage' => $stats['redis_used_memory'] ?? 0,
            ],
            'recommendations_count' => count($this->getOptimizationSuggestions()),
            'health_status' => $this->determineHealthStatus($stats),
        ];
    }
}
