<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\Query\GetAllEventsQuery;
use App\Application\UseCase\GetAllEventsUseCase;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetAllEventsUseCaseTest extends TestCase
{
    private MockObject $eventRepository;
    private GetAllEventsUseCase $useCase;

    public function testExecuteReturnsAllEventsAsDto(): void
    {
        $events = [
            new Event(
                new EventName('Event 1'),
                new Location('Location 1'),
                new Coordinates(40.7128, -74.0060),
                new EventId(1)
            ),
            new Event(
                new EventName('Event 2'),
                new Location('Location 2'),
                new Coordinates(34.0522, -118.2437),
                new EventId(2)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($events);

        $query = new GetAllEventsQuery();
        $result = $this->useCase->execute($query);

        $this->assertCount(2, $result);
        $this->assertEquals('Event 1', $result[0]->name);
        $this->assertEquals('Location 1', $result[0]->location);
        $this->assertEquals(40.7128, $result[0]->latitude);
        $this->assertEquals(-74.0060, $result[0]->longitude);
        $this->assertEquals(1, $result[0]->id);

        $this->assertEquals('Event 2', $result[1]->name);
        $this->assertEquals('Location 2', $result[1]->location);
        $this->assertEquals(34.0522, $result[1]->latitude);
        $this->assertEquals(-118.2437, $result[1]->longitude);
        $this->assertEquals(2, $result[1]->id);
    }

    public function testExecuteReturnsEmptyArrayWhenNoEvents(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $query = new GetAllEventsQuery();
        $result = $this->useCase->execute($query);

        $this->assertCount(0, $result);
        $this->assertEquals([], $result);
    }

    protected function setUp(): void
    {
        // @var EventRepositoryInterface|MockObject $eventRepository
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->useCase = new GetAllEventsUseCase($this->eventRepository);
    }
}
