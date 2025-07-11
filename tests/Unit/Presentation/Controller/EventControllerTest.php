<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\GetAllEventsQuery;
use App\Application\Query\GetEventByIdQuery;
use App\Application\Query\GetPaginatedEventsQuery;
use App\Application\UseCase\GetAllEventsUseCaseInterface;
use App\Application\UseCase\GetEventByIdUseCaseInterface;
use App\Application\UseCase\GetPaginatedEventsUseCaseInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Validation\ValidationResult;
use App\Presentation\Controller\EventController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventControllerTest extends TestCase
{
    /** @var GetAllEventsUseCaseInterface&MockObject */
    private $getAllEventsUseCase;

    /** @var GetEventByIdUseCaseInterface&MockObject */
    private $getEventByIdUseCase;

    /** @var GetPaginatedEventsUseCaseInterface&MockObject */
    private $getPaginatedEventsUseCase;

    /** @var EventRepositoryInterface&MockObject */
    private $eventRepository;

    /** @var PaginationValidator&MockObject */
    private $paginationValidator;

    /** @var EventIdValidator&MockObject */
    private $eventIdValidator;

    /** @var ResponseManager&MockObject */
    private $responseManager;

    private EventController $controller;

    public function testIndexReturnsAllEvents(): void
    {
        $events = [
            $this->createEventDto(1, 'Event 1', 'Location 1', 40.7128, -74.0060),
            $this->createEventDto(2, 'Event 2', 'Location 2', 34.0522, -118.2437),
        ];

        $this->getAllEventsUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetAllEventsQuery::class))
            ->willReturn($events);

        // Mock the response manager to send success
        $expectedData = [
            ['event_name' => 'Event 1', 'location' => 'Location 1', 'latitude' => 40.7128, 'longitude' => -74.0060, 'id' => 1, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => '2023-01-01 00:00:00'],
            ['event_name' => 'Event 2', 'location' => 'Location 2', 'latitude' => 34.0522, 'longitude' => -118.2437, 'id' => 2, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => '2023-01-01 00:00:00']
        ];
        
        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedData);

        $this->controller->index();
    }

    public function testIndexReturnsEmptyArrayWhenNoEvents(): void
    {
        $this->getAllEventsUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetAllEventsQuery::class))
            ->willReturn([]);

        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        $this->assertEquals([], $decodedOutput);
    }

    public function testShowReturnsSpecificEvent(): void
    {
        $event = $this->createEventDto(1, 'Test Event', 'Test Location', 40.7128, -74.0060);

        $this->getEventByIdUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetEventByIdQuery::class))
            ->willReturn($event);

        ob_start();
        $this->controller->show();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        $this->assertEquals('Test Event', $decodedOutput['event_name']);
        $this->assertEquals('Test Location', $decodedOutput['location']);
        $this->assertEquals(1, $decodedOutput['id']);
    }

    public function testShowReturnsErrorWhenEventNotFound(): void
    {
        $this->getEventByIdUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetEventByIdQuery::class))
            ->willReturn(null);

        ob_start();
        $this->controller->show();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        $this->assertArrayHasKey('error', $decodedOutput);
        $this->assertEquals('Event not found', $decodedOutput['error']);
    }

    public function testShowWithValidIdParameter(): void
    {
        $event = $this->createEventDto(5, 'Event 5', 'Location 5', 48.8566, 2.3522);

        $this->getEventByIdUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetEventByIdQuery $query) {
                return $query->id === 5;
            }))
            ->willReturn($event);

        ob_start();
        $this->controller->show(['id' => '5']);
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        $this->assertEquals('Event 5', $decodedOutput['event_name']);
        $this->assertEquals('Location 5', $decodedOutput['location']);
        $this->assertEquals(5, $decodedOutput['id']);
    }

    public function testShowWithInvalidIdParameter(): void
    {
        // Mock the validator to return a validation error
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->method('isValid')->willReturn(false);
        $validationResult->method('getFirstError')->willReturn('Event ID must be a positive integer');
        
        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('0')
            ->willReturn($validationResult);

        // Mock the response manager to send error
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID must be a positive integer', $this->anything());

        $this->controller->show(['id' => '0']);
    }

    public function testShowWithNegativeIdParameter(): void
    {
        // Mock the validator to return a validation error
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->method('isValid')->willReturn(false);
        $validationResult->method('getFirstError')->willReturn('Event ID must be a positive integer');
        
        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('-1')
            ->willReturn($validationResult);

        // Mock the response manager to send error
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID must be a positive integer', $this->anything());

        $this->controller->show(['id' => '-1']);
    }

    public function testDebugReturnsSystemInformation(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(4);

        ob_start();
        $this->controller->debug();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        $this->assertEquals(4, $decodedOutput['event_count']);
        $this->assertArrayHasKey('process_id', $decodedOutput);
        $this->assertArrayHasKey('timestamp', $decodedOutput);
        $this->assertTrue($decodedOutput['pooling_enabled']);
    }

    public function testPaginatedReturnsFirstPage(): void
    {
        $events = [
            $this->createEventDto(1, 'Event 1', 'Location 1', 40.7128, -74.0060),
            $this->createEventDto(2, 'Event 2', 'Location 2', 34.0522, -118.2437),
        ];

        $paginatedResponse = PaginatedResponse::create($events, 10, 1, 2);

        $this->getPaginatedEventsUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetPaginatedEventsQuery::class))
            ->willReturn($paginatedResponse);

        ob_start();
        $this->controller->paginated();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        
        $this->assertCount(2, $decodedOutput['data']);
        $this->assertEquals('Event 1', $decodedOutput['data'][0]['event_name']);
        $this->assertEquals('Event 2', $decodedOutput['data'][1]['event_name']);
        
        $this->assertEquals(1, $decodedOutput['pagination']['current_page']);
        $this->assertEquals(2, $decodedOutput['pagination']['page_size']);
        $this->assertEquals(10, $decodedOutput['pagination']['total_items']);
        $this->assertEquals(5, $decodedOutput['pagination']['total_pages']);
    }

    public function testPaginatedWithQueryParameters(): void
    {
        $_GET['page'] = '2';
        $_GET['page_size'] = '5';
        $_GET['sort_by'] = 'event_name';
        $_GET['sort_direction'] = 'DESC';

        $events = [
            $this->createEventDto(3, 'Event 3', 'Location 3', 51.5074, -0.1278),
        ];

        $paginatedResponse = PaginatedResponse::create($events, 12, 2, 5);

        $this->getPaginatedEventsUseCase
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (GetPaginatedEventsQuery $query) {
                return $query->pagination->page === 2
                    && $query->pagination->pageSize === 5
                    && $query->pagination->sortBy === 'event_name'
                    && $query->pagination->sortDirection === 'DESC';
            }))
            ->willReturn($paginatedResponse);

        ob_start();
        $this->controller->paginated();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        
        $this->assertCount(1, $decodedOutput['data']);
        $this->assertEquals(2, $decodedOutput['pagination']['current_page']);
        $this->assertEquals(5, $decodedOutput['pagination']['page_size']);

        // Clean up $_GET
        unset($_GET['page'], $_GET['page_size'], $_GET['sort_by'], $_GET['sort_direction']);
    }

    public function testPaginatedWithInvalidParameters(): void
    {
        $_GET['page'] = '0';
        $_GET['page_size'] = '101';
        $_GET['sort_by'] = 'invalid_field';

        ob_start();
        $this->controller->paginated();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertJson($output);
        $decodedOutput = json_decode($output, true);
        
        $this->assertArrayHasKey('error', $decodedOutput);
        $this->assertStringContainsString('Invalid pagination parameters', $decodedOutput['error']);

        // Clean up $_GET
        unset($_GET['page'], $_GET['page_size'], $_GET['sort_by']);
    }

    protected function setUp(): void
    {
        $this->getAllEventsUseCase = $this->createMock(GetAllEventsUseCaseInterface::class);
        $this->getEventByIdUseCase = $this->createMock(GetEventByIdUseCaseInterface::class);
        $this->getPaginatedEventsUseCase = $this->createMock(GetPaginatedEventsUseCaseInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->paginationValidator = $this->createMock(PaginationValidator::class);
        $this->eventIdValidator = $this->createMock(EventIdValidator::class);
        $this->responseManager = $this->createMock(ResponseManager::class);

        $this->controller = new EventController(
            $this->getAllEventsUseCase,
            $this->getEventByIdUseCase,
            $this->getPaginatedEventsUseCase,
            $this->eventRepository,
            $this->paginationValidator,
            $this->eventIdValidator,
            $this->responseManager
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    private function createEventDto(int $id, string $name, string $location, float $latitude, float $longitude): EventDto
    {
        return new EventDto(
            $id,
            $name,
            $location,
            $latitude,
            $longitude,
            '2023-01-01 00:00:00',
            '2023-01-01 00:00:00'
        );
    }
}
