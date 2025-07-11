<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Command\CreateEventCommand;
use App\Application\DTO\EventDto;
use App\Application\Mapper\EventMapper;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;

final class CreateEventUseCase implements CreateEventUseCaseInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(CreateEventCommand $command): EventDto
    {
        $event = new Event(
            new EventName($command->name),
            new Location($command->location),
            new Coordinates($command->latitude, $command->longitude)
        );

        $savedEvent = $this->eventRepository->save($event);

        return EventMapper::toDto($savedEvent);
    }
}
