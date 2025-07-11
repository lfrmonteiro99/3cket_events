<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Query\GetAllEventsQuery;
use App\Application\Query\GetEventByIdQuery;
use App\Application\Query\GetPaginatedEventsQuery;
use App\Application\Query\PaginationQuery;
use App\Application\UseCase\GetAllEventsUseCaseInterface;
use App\Application\UseCase\GetEventByIdUseCaseInterface;
use App\Application\UseCase\GetPaginatedEventsUseCaseInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheAction;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Presentation\Response\HttpStatus;
use App\Presentation\Response\JsonResponse;
use InvalidArgumentException;

class EventController
{
    public function __construct(
        private readonly GetAllEventsUseCaseInterface $getAllEventsUseCase,
        private readonly GetEventByIdUseCaseInterface $getEventByIdUseCase,
        private readonly GetPaginatedEventsUseCaseInterface $getPaginatedEventsUseCase,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly PaginationValidator $paginationValidator,
        private readonly EventIdValidator $eventIdValidator,
        private readonly ResponseManager $responseManager
    ) {
    }

    public function index(): void
    {
        try {
            $query = new GetAllEventsQuery();
            $eventDtos = $this->getAllEventsUseCase->execute($query);

            $result = array_map(fn ($dto) => $dto->toArray(), $eventDtos);
            $this->responseManager->sendSuccess($result);
        } catch (\Exception $e) {
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function paginated(): void
    {
        try {
            // Get pagination parameters from query string
            $paginationData = [
                'page' => $_GET['page'] ?? 1,
                'page_size' => $_GET['page_size'] ?? 10,
                'sort_by' => $_GET['sort_by'] ?? 'id',
                'sort_direction' => $_GET['sort_direction'] ?? 'ASC',
            ];

            // Validate pagination parameters using strategy
            $validationResult = $this->paginationValidator->validate($paginationData);
            if (!$validationResult->isValid()) {
                $this->responseManager->sendError(
                    'Invalid pagination parameters: ' . $validationResult->getFirstError(),
                    HttpStatus::BAD_REQUEST
                );
                return;
            }

            // Extract validated values
            $page = (int) $paginationData['page'];
            $pageSize = (int) $paginationData['page_size'];
            $sortBy = $paginationData['sort_by'];
            $sortDirection = $paginationData['sort_direction'];

            $paginationQuery = new PaginationQuery($page, $pageSize, $sortBy, $sortDirection);
            $query = new GetPaginatedEventsQuery($paginationQuery);
            
            $paginatedResponse = $this->getPaginatedEventsUseCase->execute($query);
            
            // Convert DTOs to arrays
            $data = array_map(fn ($dto) => $dto->toArray(), $paginatedResponse->data);
            
            // Create response with pagination metadata
            $response = [
                'data' => $data,
                'pagination' => $paginatedResponse->toArray()['pagination'],
            ];
            
            $this->responseManager->sendSuccess($response);
        } catch (InvalidArgumentException $e) {
            $this->responseManager->sendError('Invalid pagination parameters: ' . $e->getMessage(), HttpStatus::BAD_REQUEST);
        } catch (\Exception $e) {
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array<string, string> $parameters
     */
    public function show(array $parameters = []): void
    {
        try {
            // Extract ID from parameters, fallback to 1 for backwards compatibility with /address
            $idParam = isset($parameters['id']) ? $parameters['id'] : '1';
            
            // Validate event ID using strategy
            $validationResult = $this->eventIdValidator->validate($idParam);
            if (!$validationResult->isValid()) {
                $this->responseManager->sendError($validationResult->getFirstError(), HttpStatus::BAD_REQUEST);
                return;
            }
            
            $id = (int) $idParam;

            $query = new GetEventByIdQuery($id);
            $eventDto = $this->getEventByIdUseCase->execute($query);

            if ($eventDto === null) {
                $this->responseManager->sendNotFound('Event not found');
                return;
            }

            $this->responseManager->sendSuccess($eventDto->toArray());
        } catch (InvalidArgumentException $e) {
            $this->responseManager->sendError('Invalid event ID', HttpStatus::BAD_REQUEST);
        } catch (\Exception $e) {
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function debug(): void
    {
        try {
            $count = $this->eventRepository->count();
            $processId = getmypid();

            $cacheStats = [];

            if ($this->eventRepository instanceof CachedEventRepository) {
                $cacheStats = $this->eventRepository->getCacheStats();
            }

            $connectionInfo = [
                'process_id' => $processId,
                'event_count' => $count,
                'timestamp' => date('Y-m-d H:i:s'),
                'pooling_enabled' => true,
                'caching_enabled' => $this->eventRepository instanceof CachedEventRepository,
                'cache_stats' => $cacheStats,
                'message' => 'Connection pooling and caching active - check logs for cache hits/misses',
            ];

            $this->responseManager->sendSuccess($connectionInfo);
        } catch (\Exception $e) {
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function cache(): void
    {
        try {
            if (!$this->eventRepository instanceof CachedEventRepository) {
                $this->responseManager->sendError('Caching is not enabled', HttpStatus::BAD_REQUEST);
                return;
            }

            $actionValue = $_GET['action'] ?? 'stats';
            $action = CacheAction::fromString($actionValue);

            switch ($action) {
                case CacheAction::CLEAR:
                    $cleared = $this->eventRepository->clearCache();
                    $this->responseManager->sendSuccess([
                        'action' => $action->value,
                        'action_name' => $action->getDisplayName(),
                        'success' => $cleared,
                        'message' => $cleared ? 'Cache cleared successfully' : 'Failed to clear cache',
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]);
                    break;

                case CacheAction::STATS:
                default:
                    $stats = $this->eventRepository->getCacheStats();
                    $this->responseManager->sendSuccess([
                        'action' => $action->value,
                        'action_name' => $action->getDisplayName(),
                        'cache_stats' => $stats,
                        'timestamp' => date('Y-m-d H:i:s'),
                        'available_actions' => array_map(fn ($case) => $case->value, CacheAction::cases()),
                    ]);
                    break;
            }

        } catch (\Exception $e) {
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
