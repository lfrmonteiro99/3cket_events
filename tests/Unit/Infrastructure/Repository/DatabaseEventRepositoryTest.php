<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Repository;

use App\Domain\Entity\Event;
use App\Domain\ValueObject\EventId;
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
            'event_name' => 'Test Event',
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
            'event_name' => 'Test Event',
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
        $this->repository = new DatabaseEventRepository($this->mockPdo, new \App\Infrastructure\Logging\NullLogger());
    }
}
