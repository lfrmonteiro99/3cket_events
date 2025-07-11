<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Mapper\EventMapper;
use App\Application\Query\GetPaginatedEventsQuery;
use App\Domain\Repository\EventRepositoryInterface;

final class GetPaginatedEventsUseCase implements GetPaginatedEventsUseCaseInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return PaginatedResponse<EventDto>
     */
    public function execute(GetPaginatedEventsQuery $query): PaginatedResponse
    {
        $pagination = $query->pagination;
        
        // Get paginated events from repository (with caching)
        $events = $this->eventRepository->findPaginated($pagination);
        
        // Get total count for pagination metadata
        $totalCount = $this->eventRepository->count();
        
        // Convert events to DTOs
        $eventDtos = EventMapper::toDtoArray($events);
        
        // Create paginated response
        return PaginatedResponse::create(
            $eventDtos,
            $totalCount,
            $pagination->page,
            $pagination->pageSize
        );
    }
} 