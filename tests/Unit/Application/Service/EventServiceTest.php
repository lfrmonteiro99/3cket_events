<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Service;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\PaginationQuery;
use App\Application\Service\EventService;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventServiceTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private $eventRepository;

    private EventService $eventService;

    public function testGetAllEventsReturnsPaginatedResponse(): void
    {
        $paginationQuery = new PaginationQuery(1, 10, 'id', 'ASC');

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
            ->willReturn(25);

        $result = $this->eventService->getAllEvents($paginationQuery);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertCount(2, $result->data);
        $this->assertEquals(25, $result->totalItems);
        $this->assertEquals(1, $result->currentPage);
        $this->assertEquals(10, $result->pageSize);
        $this->assertEquals(3, $result->totalPages);

        // Verify DTOs are properly created
        $this->assertInstanceOf(EventDto::class, $result->data[0]);
        $this->assertEquals('Event 1', $result->data[0]->name);
    }

    public function testGetEventByIdReturnsEventDto(): void
    {
        $event = $this->createEvent(1, 'Test Event', 'Test Location', 40.7128, -74.0060);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->callback(function (EventId $eventId) {
                return $eventId->getValue() === 1;
            }))
            ->willReturn($event);

        $result = $this->eventService->getEventById(1);

        $this->assertInstanceOf(EventDto::class, $result);
        $this->assertEquals('Test Event', $result->name);
        $this->assertEquals('Test Location', $result->location);
        $this->assertEquals(1, $result->id);
    }

    public function testGetEventByIdReturnsNullWhenNotFound(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $result = $this->eventService->getEventById(999);

        $this->assertNull($result);
    }

    public function testGetEventCountReturnsCorrectCount(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(42);

        $result = $this->eventService->getEventCount();

        $this->assertEquals(42, $result);
    }

    public function testEventExistsReturnsTrueWhenEventExists(): void
    {
        $event = $this->createEvent(1, 'Test Event', 'Test Location', 40.7128, -74.0060);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($event);

        $result = $this->eventService->eventExists(1);

        $this->assertTrue($result);
    }

    public function testEventExistsReturnsFalseWhenEventDoesNotExist(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $result = $this->eventService->eventExists(999);

        $this->assertFalse($result);
    }

    public function testEventExistsReturnsFalseForInvalidId(): void
    {
        // Should not call repository for invalid IDs
        $this->eventRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->eventService->eventExists(-1);

        $this->assertFalse($result);
    }

    public function testValidatePaginationBusinessRulesThrowsExceptionForLargePageSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Page size must be between 1 and 100');

        // This will throw from PaginationQuery constructor, not from service
        $paginationQuery = new PaginationQuery(1, 101, 'id', 'ASC');
        $this->eventService->getAllEvents($paginationQuery);
    }

    public function testValidateEventIdThrowsExceptionForInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event ID must be a positive integer');

        $this->eventService->getEventById(0);
    }

    public function testValidateEventIdThrowsExceptionForTooLargeId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event ID too large - maximum 999999999 allowed');

        $this->eventService->getEventById(1000000000);
    }

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->eventService = new EventService($this->eventRepository);
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
