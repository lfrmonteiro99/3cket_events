<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\SearchQuery;
use App\Application\Service\EventServiceInterface;
use App\Application\Service\SearchServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheManager;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Infrastructure\Validation\ValidatorBag;
use App\Presentation\Controller\EventController;
use App\Presentation\Response\HttpStatus;
use PHPUnit\Framework\TestCase;

class EventControllerTest extends TestCase
{
    /**
     * @var EventServiceInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&SearchServiceInterface
     */
    private $searchService;

    /**
     * @var EventRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&ValidatorBag
     */
    private $validators;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&ResponseManager
     */
    private $responseManager;

    /**
     * @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    /**
     * @var CacheManager&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheManager;

    private EventController $controller;

    public function testSearchMethodUsesValidatorBagAndSearchService(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        // Mock search query parameters
        $_GET = [
            'search' => 'concert',
            'location' => 'Lisboa',
            'page' => '1',
            'page_size' => '10',
            'sort_by' => 'id',
            'sort_direction' => 'ASC',
        ];

        $expectedResponse = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Rock Concert',
                    'location' => 'Lisboa',
                    'latitude' => 38.7223,
                    'longitude' => -9.1393,
                ],
            ],
            'pagination' => [
                'total' => 1,
                'page' => 1,
                'page_size' => 10,
                'total_pages' => 1,
            ],
            'search_info' => [
                'search_term' => 'concert',
                'location_filter' => 'Lisboa',
                'geographic_search' => false,
                'date_filter' => false,
                'filters_applied' => true,
            ],
        ];

        $this->searchService
            ->expects($this->once())
            ->method('searchEventsFormatted')
            ->willReturn($expectedResponse);

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($expectedResponse);

        // Act
        $this->controller->search();

        // Clean up
        unset($_GET);
    }

    public function testSearchMethodHandlesInvalidArgumentException(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        $_GET = [
            'page' => 'invalid',
            'page_size' => 'invalid',
        ];

        // The SearchQuery constructor will throw an exception before searchEventsFormatted is called
        $this->searchService
            ->expects($this->never())
            ->method('searchEventsFormatted');

        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with($this->anything(), HttpStatus::BAD_REQUEST);

        // Act
        $this->controller->search();

        // Clean up
        unset($_GET);
    }

    public function testSearchMethodHandlesGenericException(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        $_GET = [
            'search' => 'concert',
        ];

        $this->searchService
            ->expects($this->once())
            ->method('searchEventsFormatted')
            ->willThrowException(new \Exception('Database error'));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Search error', $this->callback(function ($context) {
                return isset($context['error']) && $context['error'] === 'Database error';
            }));

        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);

        // Act
        $this->controller->search();

        // Clean up
        unset($_GET);
    }

    public function testShowMethodUsesValidatorBag(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        $eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('123')
            ->willReturn(new \App\Infrastructure\Validation\ValidationResult(true));

        $eventDto = new EventDto(
            id: 123,
            name: 'Test Event',
            location: 'Test Location',
            latitude: 38.7223,
            longitude: -9.1393
        );

        $this->eventService
            ->expects($this->once())
            ->method('getEventById')
            ->with(123)
            ->willReturn($eventDto);

        $this->logger
            ->expects($this->once())
            ->method('logBusinessEvent')
            ->with('Event retrieved', [
                'id' => 123,
                'event_name' => 'Test Event',
            ]);

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with($eventDto->toArray());

        // Act
        $this->controller->show(['id' => '123']);
    }

    public function testShowMethodHandlesMissingId(): void
    {
        // Arrange
        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Event ID is required', HttpStatus::BAD_REQUEST);

        // Act
        $this->controller->show([]);
    }

    public function testShowMethodHandlesInvalidId(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        $eventIdValidator
            ->expects($this->once())
            ->method('validate')
            ->with('invalid')
            ->willReturn(new \App\Infrastructure\Validation\ValidationResult(false, ['Invalid event ID']));

        $this->responseManager
            ->expects($this->once())
            ->method('sendError')
            ->with('Invalid event ID', HttpStatus::BAD_REQUEST);

        // Act
        $this->controller->show(['id' => 'invalid']);
    }

    public function testIndexMethodUsesValidatorBag(): void
    {
        // Arrange
        $paginationValidator = $this->createMock(PaginationValidator::class);
        $eventIdValidator = $this->createMock(EventIdValidator::class);

        $this->validators
            ->expects($this->any())
            ->method('pagination')
            ->willReturn($paginationValidator);

        $this->validators
            ->expects($this->any())
            ->method('eventId')
            ->willReturn($eventIdValidator);

        $_GET = [
            'page' => '1',
            'page_size' => '10',
            'sort_by' => 'id',
            'sort_direction' => 'ASC',
        ];

        $paginationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new \App\Infrastructure\Validation\ValidationResult(true));

        $paginatedResponse = PaginatedResponse::create([], 0, 1, 10);

        $this->eventService
            ->expects($this->once())
            ->method('getAllEvents')
            ->willReturn($paginatedResponse);

        $this->logger
            ->expects($this->once())
            ->method('logBusinessEvent')
            ->with('Events listed', [
                'count' => 0,
                'page' => 1,
                'page_size' => 10,
                'total' => 0,
            ]);

        $this->responseManager
            ->expects($this->once())
            ->method('sendSuccess')
            ->with([
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
            ]);

        // Act
        $this->controller->index();

        // Clean up
        unset($_GET);
    }

    protected function setUp(): void
    {
        $this->eventService = $this->createMock(EventServiceInterface::class);
        $this->searchService = $this->createMock(SearchServiceInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->validators = $this->createMock(ValidatorBag::class);
        $this->responseManager = $this->createMock(ResponseManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->cacheManager = $this->createMock(CacheManager::class);

        $this->controller = new EventController(
            $this->eventService,
            $this->searchService,
            $this->eventRepository,
            $this->validators,
            $this->responseManager,
            $this->logger,
            $this->cacheManager
        );
    }
}
