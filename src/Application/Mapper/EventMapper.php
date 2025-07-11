<?php

declare(strict_types=1);

namespace App\Application\Mapper;

use App\Application\DTO\EventDto;
use App\Domain\Entity\Event;

final class EventMapper
{
    public static function toDto(Event $event): EventDto
    {
        return new EventDto(
            $event->getId()?->getValue(),
            $event->getName()->getValue(),
            $event->getLocation()->getValue(),
            $event->getCoordinates()->getLatitude(),
            $event->getCoordinates()->getLongitude(),
            $event->getCreatedAt()->format('Y-m-d H:i:s'),
            $event->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @param Event[] $events
     *
     * @return EventDto[]
     */
    public static function toDtoArray(array $events): array
    {
        return array_map(
            fn (Event $event) => self::toDto($event),
            $events
        );
    }
}
