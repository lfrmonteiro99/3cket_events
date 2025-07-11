<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\EventCreated;
use App\Domain\Event\EventUpdated;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use DateTimeImmutable;

final class Event
{
    private ?EventId $id;
    private EventName $name;
    private Location $location;
    private Coordinates $coordinates;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /** @var array<object> */
    private array $domainEvents = [];

    public function __construct(
        EventName $name,
        Location $location,
        Coordinates $coordinates,
        ?EventId $id = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->location = $location;
        $this->coordinates = $coordinates;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();

        // Raise domain event if this is a new event
        if ($id === null) {
            $this->raiseDomainEvent(new EventCreated($this));
        }
    }

    public function getId(): ?EventId
    {
        return $this->id;
    }

    public function getName(): EventName
    {
        return $this->name;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(EventName $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
            $this->updatedAt = new DateTimeImmutable();
            $this->raiseDomainEvent(new EventUpdated($this));
        }
    }

    public function updateLocation(Location $location): void
    {
        if (!$this->location->equals($location)) {
            $this->location = $location;
            $this->updatedAt = new DateTimeImmutable();
            $this->raiseDomainEvent(new EventUpdated($this));
        }
    }

    public function updateCoordinates(Coordinates $coordinates): void
    {
        if (!$this->coordinates->equals($coordinates)) {
            $this->coordinates = $coordinates;
            $this->updatedAt = new DateTimeImmutable();
            $this->raiseDomainEvent(new EventUpdated($this));
        }
    }

    public function distanceTo(self $other): float
    {
        return $this->coordinates->distanceTo($other->coordinates);
    }

    public function equals(self $other): bool
    {
        return $this->id !== null &&
               $other->id !== null &&
               $this->id->equals($other->id);
    }

    /**
     * @return array<object>
     */
    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'event_name' => $this->name->getValue(),
            'location' => $this->location->getValue(),
            'latitude' => $this->coordinates->getLatitude(),
            'longitude' => $this->coordinates->getLongitude(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id->getValue();
        }

        return $data;
    }

    private function raiseDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
