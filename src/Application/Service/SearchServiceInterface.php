<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\SearchQuery;

interface SearchServiceInterface
{
    /**
     * Search events with advanced filtering and pagination.
     *
     * @param SearchQuery $searchQuery
     *
     * @return PaginatedResponse<EventDto>
     */
    public function searchEvents(SearchQuery $searchQuery): PaginatedResponse;

    /**
     * Search events and return formatted response data.
     *
     * @param SearchQuery $searchQuery
     *
     * @return array<string, mixed>
     */
    public function searchEventsFormatted(SearchQuery $searchQuery): array;

    /**
     * Get search suggestions based on partial input.
     *
     * @param string $query
     * @param int    $limit
     *
     * @return array<string>
     */
    public function getSearchSuggestions(string $query, int $limit = 10): array;

    /**
     * Get popular search terms.
     *
     * @param int $limit
     *
     * @return array<string>
     */
    public function getPopularSearchTerms(int $limit = 10): array;

    /**
     * Get events near a location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @param int   $limit
     *
     * @return array<EventDto>
     */
    public function getEventsNearby(float $latitude, float $longitude, float $radius = 10.0, int $limit = 10): array;
}
