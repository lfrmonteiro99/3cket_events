<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\PaginatedResponse;
use App\Application\Mapper\EventMapper;
use App\Application\Query\SearchQuery;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;

class SearchService implements SearchServiceInterface
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    public function searchEvents(SearchQuery $searchQuery): PaginatedResponse
    {
        $startTime = microtime(true);

        // Get search results from repository
        $events = $this->eventRepository->search($searchQuery);

        // Get total count for pagination metadata
        $totalCount = $this->eventRepository->countSearch($searchQuery);

        // Transform domain entities to DTOs
        $eventDtos = EventMapper::toDtoArray($events);

        // Create paginated response
        $response = PaginatedResponse::create(
            $eventDtos,
            $totalCount,
            $searchQuery->page,
            $searchQuery->pageSize
        );

        $duration = microtime(true) - $startTime;

        // Log search operation
        $this->logger->logBusinessEvent('Advanced search performed', [
            'search_term' => $searchQuery->search,
            'location_filter' => $searchQuery->location,
            'geographic_search' => $searchQuery->hasGeographicSearch(),
            'date_filter' => $searchQuery->hasDateFilter(),
            'results_count' => count($eventDtos),
            'total_matches' => $totalCount,
            'page' => $searchQuery->page,
            'page_size' => $searchQuery->pageSize,
            'duration' => $duration,
        ]);

        // Log performance
        $this->logger->logPerformance('Search operation', $duration, [
            'type' => 'advanced_search',
            'filters_applied' => $searchQuery->hasAnyFilter(),
            'results_count' => count($eventDtos),
        ]);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public function searchEventsFormatted(SearchQuery $searchQuery): array
    {
        // Get paginated response
        $paginatedResponse = $this->searchEvents($searchQuery);

        // Convert DTOs to arrays
        $data = array_map(fn ($dto) => $dto->toArray(), $paginatedResponse->data);

        // Create formatted response with pagination metadata
        return [
            'data' => $data,
            'pagination' => $paginatedResponse->toArray()['pagination'],
            'search_info' => [
                'search_term' => $searchQuery->search,
                'location_filter' => $searchQuery->location,
                'geographic_search' => $searchQuery->hasGeographicSearch(),
                'date_filter' => $searchQuery->hasDateFilter(),
                'filters_applied' => $searchQuery->hasAnyFilter(),
            ],
        ];
    }

    public function getSearchSuggestions(string $query, int $limit = 10): array
    {
        // Get all events to extract suggestions
        $events = $this->eventRepository->findAll();

        $suggestions = [];
        $queryLower = strtolower($query);

        foreach ($events as $event) {
            $eventName = $event->getName()->getValue();
            $location = $event->getLocation()->getValue();

            // Add event names that match
            if (str_contains(strtolower($eventName), $queryLower)) {
                $suggestions[] = $eventName;
            }

            // Add locations that match
            if (str_contains(strtolower($location), $queryLower)) {
                $suggestions[] = $location;
            }
        }

        // Remove duplicates and limit results
        $suggestions = array_unique($suggestions);

        return array_slice($suggestions, 0, $limit);
    }

    public function getPopularSearchTerms(int $limit = 10): array
    {
        // For this implementation, we'll return popular Portuguese event types and locations
        // In a real application, this would come from search analytics
        $popularTerms = [
            'Festival',
            'Lisboa',
            'Porto',
            'Concerto',
            'Feira',
            'Exposição',
            'Teatro',
            'Música',
            'Arte',
            'Gastronómico',
            'Cinema',
            'Folclore',
            'Jazz',
            'Fado',
            'Medieval',
        ];

        return array_slice($popularTerms, 0, $limit);
    }

    public function getEventsNearby(float $latitude, float $longitude, float $radius = 10.0, int $limit = 10): array
    {
        // Create a search query for nearby events
        $searchQuery = new SearchQuery(
            search: null,
            location: null,
            latitude: $latitude,
            longitude: $longitude,
            radius: $radius,
            dateFrom: null,
            dateTo: null,
            page: 1,
            pageSize: $limit,
            sortBy: 'id', // Will be sorted by distance
            sortDirection: 'ASC'
        );

        $events = $this->eventRepository->search($searchQuery);

        $this->logger->logBusinessEvent('Nearby events search', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radius,
            'results_count' => count($events),
        ]);

        return EventMapper::toDtoArray($events);
    }
}
