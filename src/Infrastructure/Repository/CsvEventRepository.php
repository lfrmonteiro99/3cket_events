<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Query\PaginationQuery;
use App\Application\Query\SearchQuery;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\EventName;
use App\Domain\ValueObject\Location;
use App\Exception\FileNotFoundException;

class CsvEventRepository implements EventRepositoryInterface
{
    private string $filePath;

    /** @var array<Event> */
    private array $events = [];

    private bool $loaded = false;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function findAll(): array
    {
        $this->loadEventsIfNeeded();

        return $this->events;
    }

    public function findPaginated(PaginationQuery $query): array
    {
        $this->loadEventsIfNeeded();

        $events = array_values($this->events);

        // Sort events
        usort($events, function (Event $a, Event $b) use ($query) {
            $comparison = match ($query->sortBy) {
                'id' => $a->getId()?->getValue() <=> $b->getId()?->getValue(),
                'event_name' => $a->getName()->getValue() <=> $b->getName()->getValue(),
                'location' => $a->getLocation()->getValue() <=> $b->getLocation()->getValue(),
                'created_at' => $a->getId()?->getValue() <=> $b->getId()?->getValue(), // Use ID as proxy for created_at
                default => $a->getId()?->getValue() <=> $b->getId()?->getValue(),
            };

            return $query->getSortDirection() === 'DESC' ? -$comparison : $comparison;
        });

        // Apply pagination
        $offset = $query->getOffset();
        $limit = $query->getLimit();

        return array_slice($events, $offset, $limit);
    }

    public function findById(EventId $id): ?Event
    {
        $this->loadEventsIfNeeded();

        $idValue = $id->getValue();

        if (!isset($this->events[$idValue])) {
            return null;
        }

        return $this->events[$idValue];
    }

    public function search(SearchQuery $query): array
    {
        $this->loadEventsIfNeeded();

        $events = array_values($this->events);

        // Apply filters
        $filteredEvents = $this->applySearchFilters($events, $query);

        // Sort events
        $this->sortEvents($filteredEvents, $query);

        // Apply pagination
        $offset = $query->getOffset();
        $limit = $query->getLimit();

        return array_slice($filteredEvents, $offset, $limit);
    }

    public function countSearch(SearchQuery $query): int
    {
        $this->loadEventsIfNeeded();

        $events = array_values($this->events);
        $filteredEvents = $this->applySearchFilters($events, $query);

        return count($filteredEvents);
    }

    public function count(): int
    {
        $this->loadEventsIfNeeded();

        return count($this->events);
    }

    private function loadEventsIfNeeded(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->loadEvents();
        $this->loaded = true;
    }

    private function loadEvents(): void
    {
        if (!file_exists($this->filePath)) {
            throw new FileNotFoundException("CSV file not found: {$this->filePath}");
        }

        $file = fopen($this->filePath, 'r');

        if ($file === false) {
            throw new FileNotFoundException("Cannot open CSV file: {$this->filePath}");
        }

        $index = 1; // Start from 1 for CSV data

        while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
            if (count($row) >= 4) {
                $this->events[$index] = new Event(
                    new EventName(trim($row[0] ?? '')),
                    new Location(trim($row[1] ?? '')),
                    new Coordinates((float) trim($row[2] ?? '0'), (float) trim($row[3] ?? '0')),
                    new EventId($index)
                );
                $index++;
            }
        }

        fclose($file);
    }

    /**
     * Apply search filters to events array.
     *
     * @param Event[] $events
     *
     * @return Event[]
     */
    private function applySearchFilters(array $events, SearchQuery $query): array
    {
        return array_filter($events, function (Event $event) use ($query) {
            // Text search filter
            if ($query->hasSearch()) {
                $searchTerm = strtolower($query->search ?? '');
                $eventName = strtolower($event->getName()->getValue());
                $location = strtolower($event->getLocation()->getValue());

                if (!str_contains($eventName, $searchTerm) && !str_contains($location, $searchTerm)) {
                    return false;
                }
            }

            // Location filter
            if ($query->hasLocationFilter()) {
                $locationFilter = strtolower($query->location ?? '');
                $eventLocation = strtolower($event->getLocation()->getValue());

                if (!str_contains($eventLocation, $locationFilter)) {
                    return false;
                }
            }

            // Geographic search filter
            if ($query->hasGeographicSearch()) {
                $distance = $event->getCoordinates()->distanceTo(
                    new Coordinates($query->latitude ?? 0.0, $query->longitude ?? 0.0)
                );

                if ($distance > $query->radius) {
                    return false;
                }
            }

            // Date filter (for CSV, we'll use the event ID as a proxy for creation date)
            if ($query->hasDateFilter()) {
                // For CSV implementation, we'll skip date filtering since we don't have actual dates
                // In a real implementation, you would parse dates from the CSV
                // For now, we'll just return true to not break the search
                return true;
            }

            return true;
        });
    }

    /**
     * Sort events array.
     *
     * @param Event[] $events
     */
    private function sortEvents(array &$events, SearchQuery $query): void
    {
        usort($events, function (Event $a, Event $b) use ($query) {
            // Special sorting for geographic search - sort by distance
            if ($query->hasGeographicSearch() && $query->sortBy === 'id') {
                $distanceA = $a->getCoordinates()->distanceTo(
                    new Coordinates($query->latitude ?? 0.0, $query->longitude ?? 0.0)
                );
                $distanceB = $b->getCoordinates()->distanceTo(
                    new Coordinates($query->latitude ?? 0.0, $query->longitude ?? 0.0)
                );

                $comparison = $distanceA <=> $distanceB;
            } else {
                // Regular sorting
                $comparison = match ($query->sortBy) {
                    'id' => $a->getId()?->getValue() <=> $b->getId()?->getValue(),
                    'event_name' => $a->getName()->getValue() <=> $b->getName()->getValue(),
                    'location' => $a->getLocation()->getValue() <=> $b->getLocation()->getValue(),
                    'created_at' => $a->getId()?->getValue() <=> $b->getId()?->getValue(), // Use ID as proxy
                    default => $a->getId()?->getValue() <=> $b->getId()?->getValue(),
                };
            }

            return $query->getSortDirection() === 'DESC' ? -$comparison : $comparison;
        });
    }
}
