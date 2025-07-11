<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Query\PaginationQuery;
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

        while (($line = fgetcsv($file)) !== false) {
            if (count($line) >= 4) {
                $this->events[$index] = new Event(
                    new EventName(trim($line[0] ?? '')),
                    new Location(trim($line[1] ?? '')),
                    new Coordinates((float) trim($line[2] ?? '0'), (float) trim($line[3] ?? '0')),
                    new EventId($index)
                );
                $index++;
            }
        }

        fclose($file);
    }
}
