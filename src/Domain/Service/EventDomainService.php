<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;

final class EventDomainService
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Find events within a certain distance from given coordinates.
     *
     * @param Coordinates $coordinates
     * @param float       $radiusInKm
     *
     * @return Event[]
     */
    public function findEventsWithinRadius(Coordinates $coordinates, float $radiusInKm): array
    {
        $allEvents = $this->eventRepository->findAll();
        $eventsWithinRadius = [];

        foreach ($allEvents as $event) {
            $distance = $coordinates->distanceTo($event->getCoordinates());

            if ($distance <= $radiusInKm) {
                $eventsWithinRadius[] = $event;
            }
        }

        return $eventsWithinRadius;
    }

    /**
     * Find the nearest event to given coordinates.
     */
    public function findNearestEvent(Coordinates $coordinates): ?Event
    {
        $allEvents = $this->eventRepository->findAll();

        if (empty($allEvents)) {
            return null;
        }

        $nearestEvent = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($allEvents as $event) {
            $distance = $coordinates->distanceTo($event->getCoordinates());

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestEvent = $event;
            }
        }

        return $nearestEvent;
    }

    /**
     * Check if event name is unique.
     */
    public function isEventNameUnique(string $eventName): bool
    {
        $allEvents = $this->eventRepository->findAll();

        foreach ($allEvents as $event) {
            if ($event->getName()->getValue() === $eventName) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the center point of all events.
     */
    public function calculateEventsCenter(): ?Coordinates
    {
        $allEvents = $this->eventRepository->findAll();

        if (empty($allEvents)) {
            return null;
        }

        $totalLatitude = 0;
        $totalLongitude = 0;
        $count = count($allEvents);

        foreach ($allEvents as $event) {
            $coords = $event->getCoordinates();
            $totalLatitude += $coords->getLatitude();
            $totalLongitude += $coords->getLongitude();
        }

        return new Coordinates(
            $totalLatitude / $count,
            $totalLongitude / $count
        );
    }
}
