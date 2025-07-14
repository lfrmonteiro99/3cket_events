<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Service\EventServiceInterface;
use App\Application\Service\SearchServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Validation\ValidationResult;
use App\Presentation\Controller\EventController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventControllerServiceTest extends TestCase
{
    /**
     * @var EventServiceInterface&MockObject
     */
    private $eventService;

    /**
     * @var MockObject&SearchServiceInterface
     */
    private $searchService;

    /**
     * @var EventRepositoryInterface&MockObject
     */
    private $eventRepository;

    /**
     * @var MockObject&PaginationValidator
     */
    private $paginationValidator;

    /**
     * @var EventIdValidator&MockObject
     */
    private $eventIdValidator;

    /**
     * @var MockObject&ResponseManager
     */
    private $responseManager;

    /**
     * @var \App\Infrastructure\Validation\ValidatorBag&MockObject
     */
    private $validatorBag;

    /**
     * @var \App\Infrastructure\Cache\CacheManager&MockObject
     */
    private $cacheManager;

    private EventController $controller;

    public function testIndexUsesServiceLayer(): void
    {
        $eventDtos = [
            $this->createEventDto(1, 'Event 1', 'Location 1', 40.7128, -74.0060),
            $this->createEventDto(2, 'Event 2', 'Location 2', 34.0522, -118.2437),
        ];

        $paginatedResponse = PaginatedResponse::create($eventDtos, 10, 1, 10);

        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getAllEvents')
            ->with($this->isInstanceOf(\App\Application\Query\PaginationQuery::class))
            ->willReturn($paginatedResponse);

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

    public function testShowUsesServiceLayer(): void
    {
        $eventDto = $this->createEventDto(5, 'Event 5', 'Location 5', 48.8566, 2.3522);

        $this->eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('5')
            ->willReturn(ValidationResult::success());

        $this->eventService
            ->expects($this->once())
            ->method('getEventById')
            ->with(5)
            ->willReturn($eventDto);

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

    public function testShowReturnsNotFoundWhenServiceReturnsNull(): void
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

    public function testDebugUsesServiceLayer(): void
    {
        $this->eventService
            ->expects($this->once())
            ->method('getEventCount')
            ->willReturn(42);

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($this->callback(function ($data) {
                return $data['event_count'] === 42
                    && isset($data['process_id'])
                    && isset($data['timestamp'])
                    && $data['pooling_enabled'] === true;
            }));

        $this->controller->debug();
    }

    public function testShowRequiresIdParameter(): void
    {
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID is required', $this->anything());

        $this->controller->show();
    }

    public function testIndexWithInvalidParameters(): void
    {
        $_GET['page'] = '0';

        $this->paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(ValidationResult::failure(['Page must be a positive integer']));

        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Invalid pagination parameters: Page must be a positive integer', $this->anything());

        $this->controller->index();

        unset($_GET['page']);
    }

    protected function setUp(): void
    {
        $this->eventService = $this->createMock(EventServiceInterface::class);
        $this->searchService = $this->createMock(SearchServiceInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->paginationValidator = $this->createMock(PaginationValidator::class);
        $this->eventIdValidator = $this->createMock(EventIdValidator::class);
        $this->responseManager = $this->createMock(ResponseManager::class);
        $this->validatorBag = $this->createMock(\App\Infrastructure\Validation\ValidatorBag::class);
        $this->cacheManager = $this->createMock(\App\Infrastructure\Cache\CacheManager::class);

        // Set up the ValidatorBag mock to return the correct validator mocks
        $this->validatorBag->method('pagination')->willReturn($this->paginationValidator);
        $this->validatorBag->method('eventId')->willReturn($this->eventIdValidator);

        $this->controller = new EventController(
            $this->eventService,
            $this->searchService,
            $this->eventRepository,
            $this->validatorBag,
            $this->responseManager,
            new \App\Infrastructure\Logging\NullLogger(),
            $this->cacheManager
        );
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
