<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Repository;

use App\Domain\Entity\Event;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use App\Infrastructure\Repository\DatabaseEventRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DatabaseEventRepositoryTest extends TestCase
{
    /** @var MockObject&PDO */
    private $mockPdo;

    /** @var MockObject&PDOStatement */
    private $mockStatement;

    private DatabaseEventRepository $repository;

    public function testFindAllReturnsAllEvents(): void
    {
        $eventData = [
            'id' => 1,
            'name' => 'Test Event',
            'location' => 'Test Location',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ];

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events ORDER BY id')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->mockStatement->expects($this->exactly(2))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls($eventData, false);

        $events = $this->repository->findAll();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(Event::class, $events[0]);
        $this->assertEquals('Test Event', $events[0]->getName()->getValue());
    }

    public function testFindByIdReturnsEventWhenFound(): void
    {
        $eventData = [
            'id' => 1,
            'name' => 'Test Event',
            'location' => 'Test Location',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ];

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events WHERE id = ? LIMIT 1')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([1])
            ->willReturn(true);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($eventData);

        $event = $this->repository->findById(new EventId(1));

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->getName()->getValue());
        $this->assertNotNull($event->getId());
        $this->assertEquals(1, $event->getId()->getValue());
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events WHERE id = ? LIMIT 1')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([999])
            ->willReturn(true);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $event = $this->repository->findById(new EventId(999));

        $this->assertNull($event);
    }

    public function testCountReturnsCorrectNumber(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with('SELECT COUNT(*) FROM events')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('5');

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }

    public function testSaveInsertsNewEventWhenNoId(): void
    {
        $event = new Event(
            new EventName('New Event'),
            new Location('New Location'),
            new Coordinates(40.7128, -74.0060)
        );

        // Mock insert
        $this->mockPdo->expects($this->exactly(2))
            ->method('prepare')
            ->with($this->callback(function ($query) {
                return $query === 'INSERT INTO events (name, location, latitude, longitude) VALUES (?, ?, ?, ?)' ||
                       $query === 'SELECT * FROM events WHERE id = ? LIMIT 1';
            }))
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function ($params) {
                return $params === ['New Event', 'New Location', 40.7128, -74.0060] ||
                       $params === [1];
            }))
            ->willReturn(true);

        $this->mockPdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('1');

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 1,
                'name' => 'New Event',
                'location' => 'New Location',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ]);

        $savedEvent = $this->repository->save($event);

        $this->assertInstanceOf(Event::class, $savedEvent);
        $this->assertNotNull($savedEvent->getId());
        $this->assertEquals(1, $savedEvent->getId()->getValue());
    }

    public function testSaveUpdatesExistingEventWhenHasId(): void
    {
        $event = new Event(
            new EventName('Updated Event'),
            new Location('Updated Location'),
            new Coordinates(40.7128, -74.0060),
            new EventId(1)
        );

        // Mock update
        $this->mockPdo->expects($this->exactly(2))
            ->method('prepare')
            ->with($this->callback(function ($query) {
                return $query === 'UPDATE events SET name = ?, location = ?, latitude = ?, longitude = ? WHERE id = ?' ||
                       $query === 'SELECT * FROM events WHERE id = ? LIMIT 1';
            }))
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function ($params) {
                return $params === ['Updated Event', 'Updated Location', 40.7128, -74.0060, 1] ||
                       $params === [1];
            }))
            ->willReturn(true);

        $this->mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 1,
                'name' => 'Updated Event',
                'location' => 'Updated Location',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ]);

        $savedEvent = $this->repository->save($event);

        $this->assertInstanceOf(Event::class, $savedEvent);
        $this->assertEquals('Updated Event', $savedEvent->getName()->getValue());
    }

    public function testDeleteRemovesEvent(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM events WHERE id = ?')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([1])
            ->willReturn(true);

        $this->mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->repository->delete(new EventId(1));

        $this->assertTrue($result);
    }

    public function testNextIdReturnsNewEventId(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with('SELECT MAX(id) FROM events')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('5');

        $nextId = $this->repository->nextId();

        $this->assertEquals(6, $nextId->getValue());
    }

    public function testPrepareIsCalledOncePerQuery(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with('SELECT COUNT(*) FROM events')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('3');

        $count = $this->repository->count();

        $this->assertEquals(3, $count);
    }

    public function testStatementExecuteIsCalledWithCorrectParameters(): void
    {
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events WHERE id = ? LIMIT 1')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([42])
            ->willReturn(true);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $event = $this->repository->findById(new EventId(42));

        $this->assertNull($event);
    }

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->repository = new DatabaseEventRepository($this->mockPdo);
    }
}
