<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Query\PaginationQuery;
use App\Application\Service\EventServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheAction;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Presentation\Response\HttpStatus;
use InvalidArgumentException;

class EventController
{
    public function __construct(
        private readonly EventServiceInterface $eventService,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly PaginationValidator $paginationValidator,
        private readonly EventIdValidator $eventIdValidator,
        private readonly ResponseManager $responseManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function index(): void
    {
        try {
            // Always use pagination with default values for /events endpoint
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

            // Use service layer for business logic
            $paginatedResponse = $this->eventService->getAllEvents($paginationQuery);

            // Convert DTOs to arrays
            $data = array_map(fn ($dto) => $dto->toArray(), $paginatedResponse->data);

            // Create response with pagination metadata
            $response = [
                'data' => $data,
                'pagination' => $paginatedResponse->toArray()['pagination'],
            ];

            $paginationArray = $paginatedResponse->toArray()['pagination'];
            $this->logger->logBusinessEvent('Events listed', [
                'count' => count($data),
                'page' => $page,
                'page_size' => $pageSize,
                'total' => $paginationArray['total'] ?? 0,
            ]);

            $this->responseManager->sendSuccess($response);
        } catch (InvalidArgumentException $e) {
            $this->logger->warning('Invalid pagination parameters', [
                'error' => $e->getMessage(),
                'parameters' => $paginationData,
            ]);
            $this->responseManager->sendError('Invalid pagination parameters: ' . $e->getMessage(), HttpStatus::BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Unhandled exception in index', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array<string, string> $parameters
     */
    public function show(array $parameters = []): void
    {
        $idParam = $parameters['id'] ?? null;

        try {
            // ID parameter is required
            if (!isset($parameters['id'])) {
                $this->responseManager->sendError('Event ID is required', HttpStatus::BAD_REQUEST);

                return;
            }

            // Validate event ID using strategy
            $validationResult = $this->eventIdValidator->validate($idParam);

            if (!$validationResult->isValid()) {
                $this->responseManager->sendError($validationResult->getFirstError() ?? 'Invalid event ID', HttpStatus::BAD_REQUEST);

                return;
            }

            $id = (int) $idParam;

            // Use service layer for business logic
            $eventDto = $this->eventService->getEventById($id);

            if ($eventDto === null) {
                $this->logger->logBusinessEvent('Event not found', ['id' => $id]);
                $this->responseManager->sendNotFound('Event not found');

                return;
            }

            $this->logger->logBusinessEvent('Event retrieved', [
                'id' => $id,
                'event_name' => $eventDto->name,
            ]);

            $this->responseManager->sendSuccess($eventDto->toArray());
        } catch (InvalidArgumentException $e) {
            $this->logger->warning('Invalid event ID', [
                'error' => $e->getMessage(),
                'id_param' => $idParam ?? 'not_set',
            ]);
            $this->responseManager->sendError('Invalid event ID', HttpStatus::BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Unhandled exception in show', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function debug(): void
    {
        try {
            // Use service layer for business metrics
            $count = $this->eventService->getEventCount();
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
