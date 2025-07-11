<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\Command\CreateEventCommand;
use App\Application\UseCase\CreateEventUseCase;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateEventUseCaseTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private MockObject $eventRepository;

    private CreateEventUseCase $useCase;

    public function testExecuteCreatesEventAndReturnsDto(): void
    {
        $command = new CreateEventCommand(
            'Test Event',
            'Test Location',
            40.7128,
            -74.0060
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Event $event) {
                return $event->getName()->getValue() === 'Test Event'
                    && $event->getLocation()->getValue() === 'Test Location'
                    && $event->getCoordinates()->getLatitude() === 40.7128
                    && $event->getCoordinates()->getLongitude() === -74.0060;
            }))
            ->willReturnCallback(function (Event $event) {
                // Simulate setting an ID after save
                $reflectionClass = new \ReflectionClass($event);
                $idProperty = $reflectionClass->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($event, new \App\Domain\ValueObject\EventId(1));

                return $event;
            });

        $result = $this->useCase->execute($command);

        $this->assertEquals('Test Event', $result->name);
        $this->assertEquals('Test Location', $result->location);
        $this->assertEquals(40.7128, $result->latitude);
        $this->assertEquals(-74.0060, $result->longitude);
        $this->assertEquals(1, $result->id);
    }

    public function testExecuteCallsRepositorySaveOnce(): void
    {
        $command = new CreateEventCommand(
            'Another Event',
            'Another Location',
            34.0522,
            -118.2437
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Event $event) {
                $reflectionClass = new \ReflectionClass($event);
                $idProperty = $reflectionClass->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($event, new \App\Domain\ValueObject\EventId(2));

                return $event;
            });

        $result = $this->useCase->execute($command);

        $this->assertEquals('Another Event', $result->name);
        $this->assertEquals('Another Location', $result->location);
        $this->assertEquals(2, $result->id);
    }

    public function testExecuteCreatesEventWithCorrectValueObjects(): void
    {
        $command = new CreateEventCommand(
            'Value Object Test',
            'VO Location',
            51.5074,
            -0.1278
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Event $event) {
                // Verify the event has correct value objects
                $this->assertEquals('Value Object Test', $event->getName()->getValue());
                $this->assertEquals('VO Location', $event->getLocation()->getValue());
                $this->assertEquals(51.5074, $event->getCoordinates()->getLatitude());
                $this->assertEquals(-0.1278, $event->getCoordinates()->getLongitude());

                return true;
            }))
            ->willReturnCallback(function (Event $event) {
                $reflectionClass = new \ReflectionClass($event);
                $idProperty = $reflectionClass->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($event, new \App\Domain\ValueObject\EventId(3));

                return $event;
            });

        $result = $this->useCase->execute($command);
        $this->assertEquals(3, $result->id);
    }

    public function testExecuteReturnsCorrectDtoStructure(): void
    {
        $command = new CreateEventCommand(
            'DTO Test',
            'DTO Location',
            48.8566,
            2.3522
        );

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Event $event) {
                $reflectionClass = new \ReflectionClass($event);
                $idProperty = $reflectionClass->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($event, new \App\Domain\ValueObject\EventId(4));

                return $event;
            });

        $result = $this->useCase->execute($command);

        $this->assertEquals(4, $result->id);
        $this->assertEquals('DTO Test', $result->name);
        $this->assertEquals('DTO Location', $result->location);
        $this->assertEquals(48.8566, $result->latitude);
        $this->assertEquals(2.3522, $result->longitude);
    }

    protected function setUp(): void
    {
        // @var EventRepositoryInterface|MockObject $eventRepository
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->useCase = new CreateEventUseCase($this->eventRepository);
    }
}
