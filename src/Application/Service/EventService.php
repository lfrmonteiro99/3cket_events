<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Mapper\EventMapper;
use App\Application\Query\PaginationQuery;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;

class EventService implements EventServiceInterface
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository
    ) {
    }

    /**
     * Get all events with pagination
     * Business logic: Apply pagination, sorting, and data transformation.
     *
     * @param PaginationQuery $paginationQuery
     *
     * @return PaginatedResponse<EventDto>
     */
    public function getAllEvents(PaginationQuery $paginationQuery): PaginatedResponse
    {
        // PaginationQuery constructor already validates basic constraints
        // Additional business logic validation can be added here if needed

        // Get paginated events from repository
        $events = $this->eventRepository->findPaginated($paginationQuery);

        // Get total count for pagination metadata
        $totalCount = $this->eventRepository->count();

        // Business logic: Transform domain entities to DTOs
        $eventDtos = EventMapper::toDtoArray($events);

        // Business logic: Create paginated response with business metadata
        return PaginatedResponse::create(
            $eventDtos,
            $totalCount,
            $paginationQuery->page,
            $paginationQuery->pageSize
        );
    }

    /**
     * Get a single event by ID
     * Business logic: Validate ID and handle not found cases.
     *
     * @param int $id
     *
     * @return null|EventDto
     */
    public function getEventById(int $id): ?EventDto
    {
        // Business logic: Validate ID is within business constraints
        $this->validateEventId($id);

        // Create domain value object
        $eventId = new EventId($id);

        // Get event from repository
        $event = $this->eventRepository->findById($eventId);

        if ($event === null) {
            return null;
        }

        // Business logic: Transform domain entity to DTO
        return EventMapper::toDto($event);
    }

    /**
     * Get total count of events
     * Business logic: Provide business metrics.
     *
     * @return int
     */
    public function getEventCount(): int
    {
        return $this->eventRepository->count();
    }

    /**
     * Check if an event exists
     * Business logic: Existence validation for business operations.
     *
     * @param int $id
     *
     * @return bool
     */
    public function eventExists(int $id): bool
    {
        try {
            $this->validateEventId($id);
            $eventId = new EventId($id);

            return $this->eventRepository->findById($eventId) !== null;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Business rule: Validate event ID.
     *
     * @param int $id
     *
     * @throws \InvalidArgumentException
     */
    private function validateEventId(int $id): void
    {
        // Business rule: Event ID must be positive
        if ($id <= 0) {
            throw new \InvalidArgumentException('Event ID must be a positive integer');
        }

        // Business rule: Reasonable ID limits for business operations
        if ($id > 999999999) {
            throw new \InvalidArgumentException('Event ID too large - maximum 999999999 allowed');
        }
    }
}
