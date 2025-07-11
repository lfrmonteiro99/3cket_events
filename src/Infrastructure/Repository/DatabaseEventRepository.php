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
use App\Exception\EventNotFoundException;
use PDO;
use PDOException;

class DatabaseEventRepository implements EventRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->connection->prepare('SELECT * FROM events ORDER BY id');
            $stmt->execute();

            $events = [];

            while ($row = $stmt->fetch()) {
                $events[] = $this->mapRowToEvent($row);
            }

            return $events;
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function findPaginated(PaginationQuery $query): array
    {
        try {
            $sortColumn = $this->mapSortColumn($query->sortBy);
            $sortDirection = $query->getSortDirection();
            $offset = $query->getOffset();
            $limit = $query->getLimit();

            $sql = "SELECT * FROM events ORDER BY {$sortColumn} {$sortDirection} LIMIT ? OFFSET ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$limit, $offset]);

            $events = [];

            while ($row = $stmt->fetch()) {
                $events[] = $this->mapRowToEvent($row);
            }

            return $events;
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function findById(EventId $id): ?Event
    {
        try {
            $stmt = $this->connection->prepare('SELECT * FROM events WHERE id = ? LIMIT 1');
            $stmt->execute([$id->getValue()]);

            $row = $stmt->fetch();

            if (!$row) {
                return null;
            }

            return $this->mapRowToEvent($row);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function count(): int
    {
        try {
            $stmt = $this->connection->query('SELECT COUNT(*) FROM events');

            if ($stmt === false) {
                throw new \RuntimeException('Failed to prepare count query');
            }

            $result = $stmt->fetchColumn();

            return (int) ($result ?: 0);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function save(Event $event): Event
    {
        try {
            $eventId = $event->getId();

            if ($eventId === null) {
                // Create new event
                $stmt = $this->connection->prepare(
                    'INSERT INTO events (name, location, latitude, longitude) VALUES (?, ?, ?, ?)'
                );

                $stmt->execute([
                    $event->getName()->getValue(),
                    $event->getLocation()->getValue(),
                    $event->getCoordinates()->getLatitude(),
                    $event->getCoordinates()->getLongitude(),
                ]);

                $id = new EventId((int) $this->connection->lastInsertId());
                $foundEvent = $this->findById($id);

                if ($foundEvent === null) {
                    throw new \RuntimeException('Failed to retrieve created event');
                }

                return $foundEvent;
            }
            // Update existing event
            $stmt = $this->connection->prepare(
                'UPDATE events SET name = ?, location = ?, latitude = ?, longitude = ? WHERE id = ?'
            );

            $stmt->execute([
                $event->getName()->getValue(),
                $event->getLocation()->getValue(),
                $event->getCoordinates()->getLatitude(),
                $event->getCoordinates()->getLongitude(),
                $eventId->getValue(),
            ]);

            if ($stmt->rowCount() === 0) {
                throw new EventNotFoundException("Event with ID {$eventId->getValue()} not found");
            }

            $foundEvent = $this->findById($eventId);

            if ($foundEvent === null) {
                throw new EventNotFoundException("Event with ID {$eventId->getValue()} not found after update");
            }

            return $foundEvent;

        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function delete(EventId $id): bool
    {
        try {
            $stmt = $this->connection->prepare('DELETE FROM events WHERE id = ?');
            $stmt->execute([$id->getValue()]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function nextId(): EventId
    {
        try {
            $stmt = $this->connection->query('SELECT MAX(id) FROM events');

            if ($stmt === false) {
                throw new \RuntimeException('Failed to prepare max id query');
            }

            $maxId = $stmt->fetchColumn();

            return new EventId($maxId ? (int) $maxId + 1 : 1);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapRowToEvent(array $row): Event
    {
        return new Event(
            new EventName((string) $row['name']),
            new Location((string) $row['location']),
            new Coordinates((float) $row['latitude'], (float) $row['longitude']),
            new EventId((int) $row['id'])
        );
    }

    private function mapSortColumn(string $sortBy): string
    {
        return match ($sortBy) {
            'id' => 'id',
            'event_name' => 'name',
            'location' => 'location',
            'created_at' => 'created_at',
            default => 'id'
        };
    }
}
