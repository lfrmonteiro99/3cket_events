<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Service;

use App\Application\Query\SearchQuery;
use App\Application\Service\SearchService;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use App\Infrastructure\Logging\LoggerInterface;
use PHPUnit\Framework\TestCase;

class SearchServiceTest extends TestCase
{
    /**
     * @var EventRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventRepository;

    /**
     * @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    private SearchService $searchService;

    public function testSearchEventsFormattedReturnsCorrectStructure(): void
    {
        // Arrange
        $searchQuery = new SearchQuery(
            search: 'concert',
            location: 'Lisboa',
            latitude: null,
            longitude: null,
            radius: null,
            dateFrom: null,
            dateTo: null,
            page: 1,
            pageSize: 10,
            sortBy: 'id',
            sortDirection: 'ASC'
        );

        $events = [
            new Event(
                new EventName('Rock Concert'),
                new Location('Lisboa'),
                new Coordinates(38.7223, -9.1393),
                new EventId(1)
            ),
            new Event(
                new EventName('Jazz Festival'),
                new Location('Porto'),
                new Coordinates(41.1579, -8.6291),
                new EventId(2)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('search')
            ->with($searchQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countSearch')
            ->with($searchQuery)
            ->willReturn(2);

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('logBusinessEvent');

        $this->logger
            ->expects($this->once())
            ->method('logPerformance');

        // Act
        $result = $this->searchService->searchEventsFormatted($searchQuery);

        // Assert
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('search_info', $result);

        // Check data structure
        $this->assertCount(2, $result['data']);
        $this->assertEquals('Rock Concert', $result['data'][0]['event_name']);
        $this->assertEquals('Jazz Festival', $result['data'][1]['event_name']);

        // Check pagination structure
        $this->assertArrayHasKey('total_items', $result['pagination']);
        $this->assertArrayHasKey('current_page', $result['pagination']);
        $this->assertArrayHasKey('page_size', $result['pagination']);
        $this->assertEquals(2, $result['pagination']['total_items']);
        $this->assertEquals(1, $result['pagination']['current_page']);

        // Check search info structure
        $this->assertEquals('concert', $result['search_info']['search_term']);
        $this->assertEquals('Lisboa', $result['search_info']['location_filter']);
        $this->assertFalse($result['search_info']['geographic_search']);
        $this->assertFalse($result['search_info']['date_filter']);
        $this->assertTrue($result['search_info']['filters_applied']);
    }

    public function testSearchEventsFormattedWithGeographicSearch(): void
    {
        // Arrange
        $searchQuery = new SearchQuery(
            search: null,
            location: null,
            latitude: 38.7223,
            longitude: -9.1393,
            radius: 10.0,
            dateFrom: null,
            dateTo: null,
            page: 1,
            pageSize: 5,
            sortBy: 'id',
            sortDirection: 'ASC'
        );

        $events = [
            new Event(
                new EventName('Nearby Event'),
                new Location('Lisboa'),
                new Coordinates(38.7223, -9.1393),
                new EventId(1)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('search')
            ->with($searchQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countSearch')
            ->with($searchQuery)
            ->willReturn(1);

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('logBusinessEvent');

        $this->logger
            ->expects($this->once())
            ->method('logPerformance');

        // Act
        $result = $this->searchService->searchEventsFormatted($searchQuery);

        // Assert
        $this->assertTrue($result['search_info']['geographic_search']);
        $this->assertFalse($result['search_info']['date_filter']);
        $this->assertTrue($result['search_info']['filters_applied']);
    }

    public function testSearchEventsFormattedWithDateFilter(): void
    {
        // Arrange
        $searchQuery = new SearchQuery(
            search: 'festival',
            location: null,
            latitude: null,
            longitude: null,
            radius: null,
            dateFrom: '2024-01-01',
            dateTo: '2024-12-31',
            page: 1,
            pageSize: 10,
            sortBy: 'id',
            sortDirection: 'ASC'
        );

        $events = [];

        $this->eventRepository
            ->expects($this->once())
            ->method('search')
            ->with($searchQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countSearch')
            ->with($searchQuery)
            ->willReturn(0);

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('logBusinessEvent');

        $this->logger
            ->expects($this->once())
            ->method('logPerformance');

        // Act
        $result = $this->searchService->searchEventsFormatted($searchQuery);

        // Assert
        $this->assertFalse($result['search_info']['geographic_search']);
        $this->assertTrue($result['search_info']['date_filter']);
        $this->assertTrue($result['search_info']['filters_applied']);
        $this->assertEquals('festival', $result['search_info']['search_term']);
        $this->assertCount(0, $result['data']);
    }

    public function testSearchEventsFormattedWithNoFilters(): void
    {
        // Arrange
        $searchQuery = new SearchQuery(
            search: null,
            location: null,
            latitude: null,
            longitude: null,
            radius: null,
            dateFrom: null,
            dateTo: null,
            page: 1,
            pageSize: 10,
            sortBy: 'id',
            sortDirection: 'ASC'
        );

        $events = [];

        $this->eventRepository
            ->expects($this->once())
            ->method('search')
            ->with($searchQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countSearch')
            ->with($searchQuery)
            ->willReturn(0);

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('logBusinessEvent');

        $this->logger
            ->expects($this->once())
            ->method('logPerformance');

        // Act
        $result = $this->searchService->searchEventsFormatted($searchQuery);

        // Assert
        $this->assertFalse($result['search_info']['geographic_search']);
        $this->assertFalse($result['search_info']['date_filter']);
        $this->assertFalse($result['search_info']['filters_applied']);
    }

    public function testSearchEventsFormattedCallsOriginalSearchEventsMethod(): void
    {
        // Arrange
        $searchQuery = new SearchQuery(
            search: 'test',
            location: null,
            latitude: null,
            longitude: null,
            radius: null,
            dateFrom: null,
            dateTo: null,
            page: 1,
            pageSize: 10,
            sortBy: 'id',
            sortDirection: 'ASC'
        );

        $events = [];

        $this->eventRepository
            ->expects($this->once())
            ->method('search')
            ->with($searchQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countSearch')
            ->with($searchQuery)
            ->willReturn(0);

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('logBusinessEvent');

        $this->logger
            ->expects($this->once())
            ->method('logPerformance');

        // Act
        $result = $this->searchService->searchEventsFormatted($searchQuery);

        // Assert - verify that the method internally calls searchEvents
        // by checking that all the expected repository and logger calls were made
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('search_info', $result);
    }

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->searchService = new SearchService($this->eventRepository, $this->logger);
    }
}
