<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Query\PaginationQuery;
use App\Application\Query\SearchQuery;
use App\Application\Service\EventServiceInterface;
use App\Application\Service\SearchServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Cache\CacheAction;
use App\Infrastructure\Cache\CacheManager;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Repository\CachedEventRepository;
use App\Infrastructure\Response\ResponseManager;
use App\Presentation\Response\HttpStatus;
use InvalidArgumentException;

class EventController
{
    public function __construct(
        private readonly EventServiceInterface $eventService,
        private readonly SearchServiceInterface $searchService,
        private readonly EventRepositoryInterface $eventRepository,
        private readonly \App\Infrastructure\Validation\ValidatorBag $validators,
        private readonly ResponseManager $responseManager,
        private readonly LoggerInterface $logger,
        private readonly CacheManager $cacheManager
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
            $validationResult = $this->validators->pagination()->validate($paginationData);

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
            $validationResult = $this->validators->eventId()->validate($idParam);

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
            $actionValue = $_GET['action'] ?? 'stats';
            $action = CacheAction::fromString($actionValue);

            switch ($action) {
                case CacheAction::CLEAR:
                    $this->cacheManager->invalidateAll();
                    $this->responseManager->sendSuccess([
                        'action' => $action->value,
                        'action_name' => $action->getDisplayName(),
                        'success' => true,
                        'message' => 'Cache cleared successfully',
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]);
                    break;

                case CacheAction::STATS:
                default:
                    $stats = $this->cacheManager->getStats();
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

    public function cacheAnalytics(): void
    {
        try {
            $analytics = $this->cacheManager->getAnalytics();
            $this->responseManager->sendSuccess($analytics);
        } catch (\Exception $e) {
            $this->logger->error('Cache analytics error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function cacheWarmUp(): void
    {
        try {
            $this->cacheManager->warmUp();
            $this->responseManager->sendSuccess([
                'message' => 'Cache warm-up completed successfully',
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Cache warm-up error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function invalidateCache(): void
    {
        try {
            $this->cacheManager->invalidateAll();
            $this->responseManager->sendSuccess([
                'message' => 'Cache invalidated successfully',
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Cache invalidation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array<string, string> $parameters
     */
    public function invalidateEventCache(array $parameters = []): void
    {
        try {
            $eventId = $parameters['id'] ?? null;

            if (!$eventId) {
                $this->responseManager->sendError('Event ID is required', HttpStatus::BAD_REQUEST);

                return;
            }
            $this->cacheManager->invalidateEvent($eventId);
            $this->responseManager->sendSuccess([
                'message' => "Cache invalidated for event {$eventId}",
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Event cache invalidation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function invalidateSearchCache(): void
    {
        try {
            $this->cacheManager->invalidateSearch();
            $this->responseManager->sendSuccess([
                'message' => 'Search cache invalidated successfully',
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Search cache invalidation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function search(): void
    {
        try {
            // Build search query from GET parameters
            $searchData = [
                'search' => $_GET['search'] ?? null,
                'location' => $_GET['location'] ?? null,
                'latitude' => $_GET['lat'] ?? null,
                'longitude' => $_GET['lng'] ?? null,
                'radius' => isset($_GET['radius']) ? (float) $_GET['radius'] : null,
                'date_from' => $_GET['date_from'] ?? null,
                'date_to' => $_GET['date_to'] ?? null,
                'page' => isset($_GET['page']) ? (int) $_GET['page'] : 1,
                'page_size' => isset($_GET['page_size']) ? (int) $_GET['page_size'] : 10,
                'sort_by' => $_GET['sort_by'] ?? 'id',
                'sort_direction' => $_GET['sort_direction'] ?? 'ASC',
            ];

            // Validate coordinates if set
            if (
                ($searchData['latitude'] !== null && !is_numeric($searchData['latitude'])) ||
                ($searchData['longitude'] !== null && !is_numeric($searchData['longitude']))
            ) {
                $this->responseManager->sendError('Invalid coordinates: latitude and longitude must be numeric', HttpStatus::BAD_REQUEST);

                return;
            }

            $searchQuery = new SearchQuery(
                search: $searchData['search'],
                location: $searchData['location'],
                latitude: $searchData['latitude'] !== null ? (float) $searchData['latitude'] : null,
                longitude: $searchData['longitude'] !== null ? (float) $searchData['longitude'] : null,
                radius: $searchData['radius'],
                dateFrom: $searchData['date_from'],
                dateTo: $searchData['date_to'],
                page: $searchData['page'],
                pageSize: $searchData['page_size'],
                sortBy: $searchData['sort_by'],
                sortDirection: $searchData['sort_direction']
            );

            $response = $this->searchService->searchEventsFormatted($searchQuery);
            $this->responseManager->sendSuccess($response);
        } catch (InvalidArgumentException $e) {
            $this->responseManager->sendError($e->getMessage(), HttpStatus::BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function nearby(): void
    {
        try {
            $latitude = $_GET['lat'] ?? null;
            $longitude = $_GET['lng'] ?? null;
            $radius = isset($_GET['radius']) ? (float) $_GET['radius'] : 10.0;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

            if ($latitude === null || $longitude === null) {
                $this->responseManager->sendError('Latitude and longitude are required', HttpStatus::BAD_REQUEST);

                return;
            }

            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                $this->responseManager->sendError('Invalid coordinates: latitude and longitude must be numeric', HttpStatus::BAD_REQUEST);

                return;
            }

            $events = $this->searchService->getEventsNearby((float) $latitude, (float) $longitude, $radius, $limit);

            $data = array_map(fn ($dto) => $dto->toArray(), $events);

            $response = [
                'data' => $data,
                'search_center' => [
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'radius_km' => $radius,
                ],
                'results_count' => count($data),
            ];

            $this->responseManager->sendSuccess($response);

        } catch (\Exception $e) {
            $this->logger->error('Nearby search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    public function suggestions(): void
    {
        try {
            $query = $_GET['q'] ?? '';
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

            if (empty($query)) {
                // Return popular search terms if no query
                $suggestions = $this->searchService->getPopularSearchTerms($limit);
            } else {
                // Return search suggestions
                $suggestions = $this->searchService->getSearchSuggestions($query, $limit);
            }

            $this->responseManager->sendSuccess([
                'suggestions' => $suggestions,
                'query' => $query,
                'count' => count($suggestions),
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Suggestions error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->responseManager->sendError('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
