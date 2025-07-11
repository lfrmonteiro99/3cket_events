<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\Mapper\EventMapper;
use App\Application\Query\GetAllEventsQuery;
use App\Domain\Repository\EventRepositoryInterface;

final class GetAllEventsUseCase implements GetAllEventsUseCaseInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return EventDto[]
     */
    public function execute(GetAllEventsQuery $query): array
    {
        $events = $this->eventRepository->findAll();

        return EventMapper::toDtoArray($events);
    }
}
