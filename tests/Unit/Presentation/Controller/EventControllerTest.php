<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Service\EventServiceInterface;
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
    /** @var EventServiceInterface&MockObject */
    private $eventService;

    /** @var EventRepositoryInterface&MockObject */
    private $eventRepository;

    /** @var MockObject&PaginationValidator */
    private $paginationValidator;

    /** @var EventIdValidator&MockObject */
    private $eventIdValidator;

    /** @var MockObject&ResponseManager */
    private $responseManager;

    private EventController $controller;

    public function testIndexReturnsPaginatedEvents(): void
    {
        $events = [
            $this->createEventDto(1, 'Event 1', 'Location 1', 40.7128, -74.0060),
            $this->createEventDto(2, 'Event 2', 'Location 2', 34.0522, -118.2437),
        ];

        $paginatedResponse = PaginatedResponse::create($events, 10, 1, 10);

        // Mock validation to pass
        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getAllEvents')
            ->willReturn($paginatedResponse);

        // Mock the response manager to send success
        $expectedData = [
            'data' => [
                ['event_name' => 'Event 1', 'location' => 'Location 1', 'latitude' => 40.7128, 'longitude' => -74.0060, 'id' => 1, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => '2023-01-01 00:00:00'],
                ['event_name' => 'Event 2', 'location' => 'Location 2', 'latitude' => 34.0522, 'longitude' => -118.2437, 'id' => 2, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => '2023-01-01 00:00:00'],
            ],
            'pagination' => [
                'current_page' => 1,
                'page_size' => 10,
                'total_items' => 10,
                'total_pages' => 1,
                'has_next_page' => false,
                'has_previous_page' => false,
                'next_page' => null,
                'previous_page' => null,
                'start_item' => 1,
                'end_item' => 10,
            ],
        ];

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedData);

        $this->controller->index();
    }

    public function testIndexReturnsEmptyPaginatedResponseWhenNoEvents(): void
    {
        $paginatedResponse = PaginatedResponse::create([], 0, 1, 10);

        // Mock validation to pass
        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getAllEvents')
            ->willReturn($paginatedResponse);

        // Mock the response manager to send success
        $expectedData = [
            'data' => [],
            'pagination' => [
                'current_page' => 1,
                'page_size' => 10,
                'total_items' => 0,
                'total_pages' => 0,
                'has_next_page' => false,
                'has_previous_page' => false,
                'next_page' => null,
                'previous_page' => null,
                'start_item' => 0,
                'end_item' => 0,
            ],
        ];

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedData);

        $this->controller->index();
    }

    public function testIndexWithQueryParameters(): void
    {
        $_GET['page'] = '2';
        $_GET['page_size'] = '5';
        $_GET['sort_by'] = 'event_name';
        $_GET['sort_direction'] = 'DESC';

        $events = [
            $this->createEventDto(3, 'Event 3', 'Location 3', 51.5074, -0.1278),
        ];

        $paginatedResponse = PaginatedResponse::create($events, 12, 2, 5);

        // Mock validation to pass
        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getAllEvents')
            ->willReturn($paginatedResponse);

        // Mock the response manager to send success
        $expectedData = [
            'data' => [
                ['event_name' => 'Event 3', 'location' => 'Location 3', 'latitude' => 51.5074, 'longitude' => -0.1278, 'id' => 3, 'created_at' => '2023-01-01 00:00:00', 'updated_at' => '2023-01-01 00:00:00'],
            ],
            'pagination' => [
                'current_page' => 2,
                'page_size' => 5,
                'total_items' => 12,
                'total_pages' => 3,
                'has_next_page' => true,
                'has_previous_page' => true,
                'next_page' => 3,
                'previous_page' => 1,
                'start_item' => 6,
                'end_item' => 10,
            ],
        ];

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedData);

        $this->controller->index();

        // Clean up $_GET
        unset($_GET['page'], $_GET['page_size'], $_GET['sort_by'], $_GET['sort_direction']);
    }

    public function testIndexWithInvalidParameters(): void
    {
        $_GET['page'] = '0';
        $_GET['page_size'] = '101';

        // Mock validation to fail
        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::failure(['Page must be a positive integer']));

        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Invalid pagination parameters: Page must be a positive integer', $this->anything());

        $this->controller->index();

        // Clean up $_GET
        unset($_GET['page'], $_GET['page_size']);
    }

    public function testShowRequiresIdParameter(): void
    {
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID is required', $this->anything());

        $this->controller->show();
    }

    public function testShowReturnsErrorWhenEventNotFound(): void
    {
        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('999')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getEventById')
            ->with(999)
            ->willReturn(null);

        $this->responseManager
            ->expects($this->once())
            ->method('sendNotFound')
            ->with('Event not found');

        $this->controller->show(['id' => '999']);
    }

    public function testShowWithValidIdParameter(): void
    {
        $event = $this->createEventDto(5, 'Event 5', 'Location 5', 48.8566, 2.3522);

        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('5')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getEventById')
            ->with(5)
            ->willReturn($event);

        $expectedData = [
            'event_name' => 'Event 5',
            'location' => 'Location 5',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'id' => 5,
            'created_at' => '2023-01-01 00:00:00',
            'updated_at' => '2023-01-01 00:00:00',
        ];

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedData);

        $this->controller->show(['id' => '5']);
    }

    public function testShowWithInvalidIdParameter(): void
    {
        // Mock the validator to return a validation error
        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('0')
            ->willReturn(ValidationResult::failure(['Event ID must be a positive integer']));

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
        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('-1')
            ->willReturn(ValidationResult::failure(['Event ID must be a positive integer']));

        // Mock the response manager to send error
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID must be a positive integer', $this->anything());

        $this->controller->show(['id' => '-1']);
    }

    public function testDebugReturnsSystemInformation(): void
    {
        $this->eventService
            ->expects($this->once())
            ->method('getEventCount')
            ->willReturn(4);

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($this->callback(function ($data) {
                return $data['event_count'] === 4
                    && isset($data['process_id'])
                    && isset($data['timestamp'])
                    && $data['pooling_enabled'] === true;
            }));

        $this->controller->debug();
    }

    protected function setUp(): void
    {
        $this->eventService = $this->createMock(EventServiceInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->paginationValidator = $this->createMock(PaginationValidator::class);
        $this->eventIdValidator = $this->createMock(EventIdValidator::class);
        $this->responseManager = $this->createMock(ResponseManager::class);

        $this->controller = new EventController(
            $this->eventService,
            $this->eventRepository,
            $this->paginationValidator,
            $this->eventIdValidator,
            $this->responseManager,
            new \App\Infrastructure\Logging\NullLogger()
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
