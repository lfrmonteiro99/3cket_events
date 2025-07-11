<?php

declare(strict_types=1);

namespace App\Domain\Entity;

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

    /**
     * @return array<string>
     */
    public function __sleep(): array
    {
        return ['id', 'name', 'location', 'coordinates', 'createdAt', 'updatedAt'];
    }
}
