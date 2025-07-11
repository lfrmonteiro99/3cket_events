<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Entity\Event;
use DateTimeImmutable;

final class EventUpdated
{
    private Event $event;
    private DateTimeImmutable $occurredAt;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->occurredAt = new DateTimeImmutable();
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getEventName(): string
    {
        return 'event.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_name' => $this->getEventName(),
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
            'event_id' => $this->event->getId()?->getValue(),
            'event_data' => $this->event->toArray(),
        ];
    }
}
