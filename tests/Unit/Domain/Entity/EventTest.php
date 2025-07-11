<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\Event;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEventCanBeCreated(): void
    {
        $name = new EventName('Test Event');
        $location = new Location('Test Location');
        $coordinates = new Coordinates(40.7128, -74.0060);
        $id = new EventId(1);

        $event = new Event($name, $location, $coordinates, $id);

        $this->assertEquals($id, $event->getId());
        $this->assertEquals($name, $event->getName());
        $this->assertEquals($location, $event->getLocation());
        $this->assertEquals($coordinates, $event->getCoordinates());
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->getUpdatedAt());
    }

    public function testEventDistanceTo(): void
    {
        $event1 = $this->createTestEvent(
            new EventName('Event 1'),
            new Location('NYC'),
            new Coordinates(40.7128, -74.0060)
        );

        $event2 = $this->createTestEvent(
            new EventName('Event 2'),
            new Location('LA'),
            new Coordinates(34.0522, -118.2437)
        );

        $distance = $event1->distanceTo($event2);

        // Distance between NYC and LA is approximately 3944 km
        $this->assertGreaterThan(3900, $distance);
        $this->assertLessThan(4000, $distance);
    }

    public function testEventEquals(): void
    {
        $event1 = $this->createTestEvent(null, null, null, new EventId(1));
        $event2 = $this->createTestEvent(null, null, null, new EventId(2)); // Different ID

        $this->assertTrue($event1->equals($event1)); // Same instance
        $this->assertFalse($event1->equals($event2)); // Different IDs

        // Test events with same ID are equal (DDD principle)
        $event3 = $this->createTestEvent(null, null, null, new EventId(1));
        $this->assertTrue($event1->equals($event3)); // Same ID = equal
    }

    public function testEventToArray(): void
    {
        $name = new EventName('Test Event');
        $location = new Location('Test Location');
        $coordinates = new Coordinates(40.7128, -74.0060);
        $id = new EventId(1);

        $event = new Event($name, $location, $coordinates, $id);

        $array = $event->toArray();

        $this->assertEquals('Test Event', $array['event_name']);
        $this->assertEquals('Test Location', $array['location']);
        $this->assertEquals(40.7128, $array['latitude']);
        $this->assertEquals(-74.0060, $array['longitude']);
        $this->assertEquals(1, $array['id']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    private function createTestEvent(
        ?EventName $name = null,
        ?Location $location = null,
        ?Coordinates $coordinates = null,
        ?EventId $id = null
    ): Event {
        return new Event(
            $name ?? new EventName('Test Event'),
            $location ?? new Location('Test Location'),
            $coordinates ?? new Coordinates(40.7128, -74.0060),
            $id ?? new EventId(1)
        );
    }
}
