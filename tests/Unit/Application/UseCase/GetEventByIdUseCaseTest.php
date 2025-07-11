<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\Query\GetEventByIdQuery;
use App\Application\UseCase\GetEventByIdUseCase;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetEventByIdUseCaseTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private MockObject $eventRepository;

    private GetEventByIdUseCase $useCase;

    public function testExecuteReturnsEventAsDtoWhenFound(): void
    {
        $eventId = new EventId(1);
        $event = new Event(
            new EventName('Test Event'),
            new Location('Test Location'),
            new Coordinates(40.7128, -74.0060),
            $eventId
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($eventId)
            ->willReturn($event);

        $query = new GetEventByIdQuery(1);
        $result = $this->useCase->execute($query);

        $this->assertNotNull($result);
        $this->assertEquals('Test Event', $result->name);
        $this->assertEquals('Test Location', $result->location);
        $this->assertEquals(40.7128, $result->latitude);
        $this->assertEquals(-74.0060, $result->longitude);
        $this->assertEquals(1, $result->id);
    }

    public function testExecuteReturnsNullWhenEventNotFound(): void
    {
        $eventId = new EventId(999);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($eventId)
            ->willReturn(null);

        $query = new GetEventByIdQuery(999);
        $result = $this->useCase->execute($query);

        $this->assertNull($result);
    }

    public function testExecuteCallsRepositoryWithCorrectEventId(): void
    {
        $eventId = new EventId(42);
        $event = new Event(
            new EventName('Test Event'),
            new Location('Test Location'),
            new Coordinates(40.7128, -74.0060),
            $eventId
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->callback(function (EventId $id) {
                return $id->getValue() === 42;
            }))
            ->willReturn($event);

        $query = new GetEventByIdQuery(42);
        $result = $this->useCase->execute($query);

        $this->assertNotNull($result);
        $this->assertEquals(42, $result->id);
    }

    protected function setUp(): void
    {
        // @var EventRepositoryInterface|MockObject $eventRepository
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->useCase = new GetEventByIdUseCase($this->eventRepository);
    }
}
