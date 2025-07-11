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
use App\Infrastructure\Logging\LoggerInterface;
use PDO;
use PDOException;

class DatabaseEventRepository implements EventRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function findAll(): array
    {
        $startTime = microtime(true);

        try {
            $stmt = $this->connection->prepare('SELECT * FROM events ORDER BY id');
            $stmt->execute();

            $events = [];

            while ($row = $stmt->fetch()) {
                $events[] = $this->mapRowToEvent($row);
            }

            $duration = microtime(true) - $startTime;
            $this->logger->logDatabaseOperation('findAll', [
                'count' => count($events),
                'duration' => $duration,
            ]);

            return $events;
        } catch (PDOException $e) {
            $this->logger->error('Database error in findAll', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

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

    /**
     * @param array<string, mixed> $row
     */
    private function mapRowToEvent(array $row): Event
    {
        return new Event(
            new EventName((string) $row['event_name']),
            new Location((string) $row['location']),
            new Coordinates((float) $row['latitude'], (float) $row['longitude']),
            new EventId((int) $row['id'])
        );
    }

    private function mapSortColumn(string $sortBy): string
    {
        return match ($sortBy) {
            'id' => 'id',
            'event_name' => 'event_name',
            'location' => 'location',
            'created_at' => 'created_at',
            default => 'id'
        };
    }
}
