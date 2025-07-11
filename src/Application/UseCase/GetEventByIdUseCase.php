<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\Mapper\EventMapper;
use App\Application\Query\GetEventByIdQuery;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;

final class GetEventByIdUseCase implements GetEventByIdUseCaseInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(GetEventByIdQuery $query): ?EventDto
    {
        $eventId = new EventId($query->id);
        $event = $this->eventRepository->findById($eventId);

        if ($event === null) {
            return null;
        }

        return EventMapper::toDto($event);
    }
}
