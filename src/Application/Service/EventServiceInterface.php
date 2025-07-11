<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\PaginationQuery;

interface EventServiceInterface
{
    /**
     * Get all events with pagination.
     *
     * @param PaginationQuery $paginationQuery
     *
     * @return PaginatedResponse<EventDto>
     */
    public function getAllEvents(PaginationQuery $paginationQuery): PaginatedResponse;

    /**
     * Get a single event by ID.
     *
     * @param int $id
     *
     * @return null|EventDto
     */
    public function getEventById(int $id): ?EventDto;

    /**
     * Get total count of events.
     *
     * @return int
     */
    public function getEventCount(): int;

    /**
     * Check if an event exists.
     *
     * @param int $id
     *
     * @return bool
     */
    public function eventExists(int $id): bool;
}
