<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Service;

use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Service\EventDomainService;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventDomainServiceTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private MockObject $eventRepository;

    private EventDomainService $domainService;

    public function testFindEventsWithinRadiusReturnsEventsWithinRadius(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060); // NYC
        $radius = 100.0; // 100 km

        $allEvents = [
            new Event(
                new EventName('Nearby Event 1'),
                new Location('Manhattan'),
                new Coordinates(40.7589, -73.9851), // Times Square (close to NYC)
                new EventId(1)
            ),
            new Event(
                new EventName('Nearby Event 2'),
                new Location('Brooklyn'),
                new Coordinates(40.6782, -73.9442), // Brooklyn (close to NYC)
                new EventId(2)
            ),
            new Event(
                new EventName('Far Event'),
                new Location('Los Angeles'),
                new Coordinates(34.0522, -118.2437), // LA (far from NYC)
                new EventId(3)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($allEvents);

        $nearbyEvents = $this->domainService->findEventsWithinRadius($coordinates, $radius);

        $this->assertCount(2, $nearbyEvents);
        $this->assertEquals('Nearby Event 1', $nearbyEvents[0]->getName()->getValue());
        $this->assertEquals('Nearby Event 2', $nearbyEvents[1]->getName()->getValue());
    }

    public function testFindEventsWithinRadiusReturnsEmptyWhenNoEventsWithinRadius(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060); // NYC
        $radius = 10.0; // 10 km (very small radius)

        $allEvents = [
            new Event(
                new EventName('Far Event'),
                new Location('Los Angeles'),
                new Coordinates(34.0522, -118.2437), // LA (far from NYC)
                new EventId(1)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($allEvents);

        $nearbyEvents = $this->domainService->findEventsWithinRadius($coordinates, $radius);

        $this->assertCount(0, $nearbyEvents);
    }

    public function testFindNearestEventReturnsClosestEvent(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060); // NYC

        $allEvents = [
            new Event(
                new EventName('Close Event'),
                new Location('Manhattan'),
                new Coordinates(40.7589, -73.9851), // Times Square (close to NYC)
                new EventId(1)
            ),
            new Event(
                new EventName('Far Event'),
                new Location('Los Angeles'),
                new Coordinates(34.0522, -118.2437), // LA (far from NYC)
                new EventId(2)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($allEvents);

        $nearestEvent = $this->domainService->findNearestEvent($coordinates);

        $this->assertNotNull($nearestEvent);
        $this->assertEquals('Close Event', $nearestEvent->getName()->getValue());
        $this->assertNotNull($nearestEvent->getId());
        $this->assertEquals(1, $nearestEvent->getId()->getValue());
    }

    public function testFindNearestEventReturnsNullWhenNoEvents(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $nearestEvent = $this->domainService->findNearestEvent($coordinates);

        $this->assertNull($nearestEvent);
    }

    public function testIsEventNameUniqueReturnsTrueWhenNameIsUnique(): void
    {
        $eventName = 'Unique Event';

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $isUnique = $this->domainService->isEventNameUnique($eventName);

        $this->assertTrue($isUnique);
    }

    public function testIsEventNameUniqueReturnsFalseWhenNameExists(): void
    {
        $eventName = 'Existing Event';
        $existingEvent = new Event(
            new EventName($eventName),
            new Location('Some Location'),
            new Coordinates(40.7128, -74.0060),
            new EventId(1)
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$existingEvent]);

        $isUnique = $this->domainService->isEventNameUnique($eventName);

        $this->assertFalse($isUnique);
    }

    public function testCalculateEventsCenterReturnsCorrectCoordinates(): void
    {
        $allEvents = [
            new Event(
                new EventName('Event 1'),
                new Location('NYC'),
                new Coordinates(40.0, -74.0),
                new EventId(1)
            ),
            new Event(
                new EventName('Event 2'),
                new Location('LA'),
                new Coordinates(42.0, -72.0),
                new EventId(2)
            ),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($allEvents);

        $center = $this->domainService->calculateEventsCenter();

        $this->assertNotNull($center);
        $this->assertEquals(41.0, $center->getLatitude()); // (40 + 42) / 2
        $this->assertEquals(-73.0, $center->getLongitude()); // (-74 + -72) / 2
    }

    public function testCalculateEventsCenterReturnsNullWhenNoEvents(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $center = $this->domainService->calculateEventsCenter();

        $this->assertNull($center);
    }

    public function testRepositoryIsCalledOnceForFindEventsWithinRadius(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);
        $radius = 100.0;

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->domainService->findEventsWithinRadius($coordinates, $radius);
    }

    public function testRepositoryIsCalledOnceForFindNearestEvent(): void
    {
        $coordinates = new Coordinates(40.7128, -74.0060);

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->domainService->findNearestEvent($coordinates);
    }

    public function testRepositoryIsCalledOnceForIsEventNameUnique(): void
    {
        $eventName = 'Test Event';

        $this->eventRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->domainService->isEventNameUnique($eventName);
    }

    protected function setUp(): void
    {
        // @var EventRepositoryInterface|MockObject $eventRepository
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->domainService = new EventDomainService($this->eventRepository);
    }
}
