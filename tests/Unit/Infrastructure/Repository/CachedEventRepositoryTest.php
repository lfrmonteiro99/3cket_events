<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Repository;

use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use App\Infrastructure\Cache\CacheInterface;
use App\Infrastructure\Repository\CachedEventRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CachedEventRepositoryTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private $mockRepository;

    /** @var CacheInterface&MockObject */
    private $mockCache;

    private CachedEventRepository $cachedRepository;

    public function testFindAllReturnsCachedResultWhenAvailable(): void
    {
        $events = [
            new Event(
                new EventName('Test Event'),
                new Location('Test Location'),
                new Coordinates(40.7128, -74.0060),
                new EventId(1)
            ),
        ];

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('events:all')
            ->willReturn($events);

        $this->mockRepository->expects($this->never())
            ->method('findAll');

        $result = $this->cachedRepository->findAll();
        $this->assertEquals($events, $result);
    }

    public function testFindAllFetchesFromRepositoryWhenNotCached(): void
    {
        $events = [
            new Event(
                new EventName('Test Event'),
                new Location('Test Location'),
                new Coordinates(40.7128, -74.0060),
                new EventId(1)
            ),
        ];

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('events:all')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($events);

        $this->mockCache->expects($this->once())
            ->method('setWithSmartTtl')
            ->with('events:all', $events, 3600);

        $result = $this->cachedRepository->findAll();
        $this->assertEquals($events, $result);
    }

    public function testFindByIdReturnsCachedResultWhenAvailable(): void
    {
        $event = new Event(
            new EventName('Test Event'),
            new Location('Test Location'),
            new Coordinates(40.7128, -74.0060),
            new EventId(1)
        );
        $eventId = new EventId(1);

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('event:1')
            ->willReturn($event);

        $this->mockRepository->expects($this->never())
            ->method('findById');

        $result = $this->cachedRepository->findById($eventId);
        $this->assertEquals($event, $result);
    }

    public function testFindByIdFetchesFromRepositoryWhenNotCached(): void
    {
        $event = new Event(
            new EventName('Test Event'),
            new Location('Test Location'),
            new Coordinates(40.7128, -74.0060),
            new EventId(1)
        );
        $eventId = new EventId(1);

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('event:1')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findById')
            ->with($eventId)
            ->willReturn($event);

        $this->mockCache->expects($this->once())
            ->method('setWithSmartTtl')
            ->with('event:1', $event, 3600);

        $result = $this->cachedRepository->findById($eventId);
        $this->assertEquals($event, $result);
    }

    public function testFindByIdReturnsNullWhenEventNotFound(): void
    {
        $eventId = new EventId(999);

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('event:999')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findById')
            ->with($eventId)
            ->willReturn(null);

        // No cache write expected for null
        $result = $this->cachedRepository->findById($eventId);
        $this->assertNull($result);
    }

    public function testCountReturnsCachedResultWhenAvailable(): void
    {
        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('events:count')
            ->willReturn(5);

        $this->mockRepository->expects($this->never())
            ->method('count');

        $result = $this->cachedRepository->count();
        $this->assertEquals(5, $result);
    }

    public function testCountFetchesFromRepositoryWhenNotCached(): void
    {
        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('events:count')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('count')
            ->willReturn(5);

        $this->mockCache->expects($this->once())
            ->method('setWithSmartTtl')
            ->with('events:count', 5, 7200);

        $result = $this->cachedRepository->count();
        $this->assertEquals(5, $result);
    }

    public function testCacheKeyGenerationForEvents(): void
    {
        $eventId = new EventId(42);

        $this->mockCache->expects($this->once())
            ->method('get')
            ->with('event:42')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findById')
            ->with($eventId)
            ->willReturn(null);

        // No cache write expected for null
        $result = $this->cachedRepository->findById($eventId);
        $this->assertNull($result);
    }

    public function testMultipleCacheOperationsInSequence(): void
    {
        // First call - cache miss
        $this->mockCache->expects($this->exactly(2))
            ->method('get')
            ->with($this->callback(function ($key) {
                return in_array($key, ['events:count', 'events:all'], true);
            }))
            ->willReturnOnConsecutiveCalls(null, null);

        $this->mockRepository->expects($this->once())
            ->method('count')
            ->willReturn(5);

        $this->mockRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->mockCache->expects($this->once())
            ->method('setWithSmartTtl')
            ->with('events:count', 5, 7200);

        $countResult = $this->cachedRepository->count();
        $allResult = $this->cachedRepository->findAll();

        $this->assertEquals(5, $countResult);
        $this->assertEquals([], $allResult);
    }

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(EventRepositoryInterface::class);
        $this->mockCache = $this->createMock(CacheInterface::class);
        $mockAnalytics = $this->createMock(\App\Infrastructure\Cache\CacheAnalytics::class);
        $this->cachedRepository = new CachedEventRepository(
            $this->mockRepository,
            $this->mockCache,
            $mockAnalytics,
            3600
        );
    }
}
