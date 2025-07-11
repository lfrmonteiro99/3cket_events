<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\GetPaginatedEventsQuery;
use App\Application\Query\PaginationQuery;
use App\Application\UseCase\GetPaginatedEventsUseCase;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetPaginatedEventsUseCaseTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private MockObject $eventRepository;

    private GetPaginatedEventsUseCase $useCase;

    public function testExecuteReturnsPaginatedEvents(): void
    {
        $paginationQuery = new PaginationQuery(1, 2, 'id', 'ASC');
        $query = new GetPaginatedEventsQuery($paginationQuery);

        $events = [
            $this->createEvent(1, 'Event 1', 'Location 1', 40.7128, -74.0060),
            $this->createEvent(2, 'Event 2', 'Location 2', 34.0522, -118.2437),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with($paginationQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(10);

        $result = $this->useCase->execute($query);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertCount(2, $result->data);
        $this->assertEquals(10, $result->totalItems);
        $this->assertEquals(1, $result->currentPage);
        $this->assertEquals(2, $result->pageSize);
        $this->assertEquals(5, $result->totalPages);

        /** @var EventDto $firstEvent */
        $firstEvent = $result->data[0];
        $this->assertEquals(1, $firstEvent->id);
        $this->assertEquals('Event 1', $firstEvent->name);
        $this->assertEquals('Location 1', $firstEvent->location);
    }

    public function testExecuteWithEmptyResults(): void
    {
        $paginationQuery = new PaginationQuery(10, 10, 'id', 'ASC');
        $query = new GetPaginatedEventsQuery($paginationQuery);

        $this->eventRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with($paginationQuery)
            ->willReturn([]);

        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(5);

        $result = $this->useCase->execute($query);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertEmpty($result->data);
        $this->assertEquals(5, $result->totalItems);
        $this->assertEquals(10, $result->currentPage);
        $this->assertEquals(10, $result->pageSize);
        $this->assertEquals(1, $result->totalPages);
    }

    public function testExecuteWithDifferentPaginationParams(): void
    {
        $paginationQuery = new PaginationQuery(2, 5, 'event_name', 'DESC');
        $query = new GetPaginatedEventsQuery($paginationQuery);

        $events = [
            $this->createEvent(3, 'Event 3', 'Location 3', 51.5074, -0.1278),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with($paginationQuery)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(12);

        $result = $this->useCase->execute($query);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertCount(1, $result->data);
        $this->assertEquals(12, $result->totalItems);
        $this->assertEquals(2, $result->currentPage);
        $this->assertEquals(5, $result->pageSize);
        $this->assertEquals(3, $result->totalPages);
    }

    public function testExecuteReturnsCorrectEventDtos(): void
    {
        $paginationQuery = new PaginationQuery(1, 1, 'id', 'ASC');
        $query = new GetPaginatedEventsQuery($paginationQuery);

        $event = $this->createEvent(
            5,
            'Test Event',
            'Test Location',
            48.8566,
            2.3522
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with($paginationQuery)
            ->willReturn([$event]);

        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $result = $this->useCase->execute($query);

        /** @var EventDto $eventDto */
        $eventDto = $result->data[0];
        $this->assertEquals(5, $eventDto->id);
        $this->assertEquals('Test Event', $eventDto->name);
        $this->assertEquals('Test Location', $eventDto->location);
        $this->assertEquals(48.8566, $eventDto->latitude);
        $this->assertEquals(2.3522, $eventDto->longitude);
    }

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->useCase = new GetPaginatedEventsUseCase($this->eventRepository);
    }

    private function createEvent(int $id, string $name, string $location, float $latitude, float $longitude): Event
    {
        return new Event(
            new EventName($name),
            new Location($location),
            new Coordinates($latitude, $longitude),
            new EventId($id)
        );
    }
} 