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

            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
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
            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
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
            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
        }
    }

    public function search(SearchQuery $query): array
    {
        try {
            $startTime = microtime(true);

            [$sql, $params] = $this->buildSearchQuery($query, false);

            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            $events = [];

            while ($row = $stmt->fetch()) {
                $events[] = $this->mapRowToEvent($row);
            }

            $duration = microtime(true) - $startTime;
            $this->logger->logDatabaseOperation('search', [
                'count' => count($events),
                'duration' => $duration,
                'has_filters' => $query->hasAnyFilter(),
                'search_term' => $query->search,
                'location_filter' => $query->location,
                'geographic_search' => $query->hasGeographicSearch(),
                'date_filter' => $query->hasDateFilter(),
            ]);

            return $events;
        } catch (PDOException $e) {
            $this->logger->error('Database error in search', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'query' => $query,
            ]);

            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
        }
    }

    public function countSearch(SearchQuery $query): int
    {
        try {
            [$sql, $params] = $this->buildSearchQuery($query, true);

            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetchColumn();

            return (int) ($result ?: 0);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
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
            throw new \RuntimeException('Database error: ' . $e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0);
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

    /**
     * Build search query with filters.
     *
     * @return array{string, array<mixed>}
     */
    private function buildSearchQuery(SearchQuery $query, bool $countOnly = false): array
    {
        $params = [];
        $conditions = [];

        // Base query
        if ($countOnly) {
            $sql = 'SELECT COUNT(*) FROM events';
        } else {
            $sql = 'SELECT *';

            // Add distance calculation for geographic search
            if ($query->hasGeographicSearch()) {
                $sql .= ', (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance';
                $params[] = $query->latitude;
                $params[] = $query->longitude;
                $params[] = $query->latitude;
            }

            $sql .= ' FROM events';
        }

        // Text search in event name and location
        if ($query->hasSearch()) {
            // Use FULLTEXT search for better performance if search term is suitable
            $searchTerm = trim($query->search ?? '');

            if (strlen($searchTerm) >= 3 && !str_contains($searchTerm, '%')) {
                // Use FULLTEXT MATCH for performance
                $conditions[] = 'MATCH(event_name, location) AGAINST(? IN BOOLEAN MODE)';
                $params[] = $searchTerm . '*'; // Add wildcard for partial matches
            } else {
                // Fall back to LIKE for short terms or special characters
                $searchPattern = '%' . $searchTerm . '%';
                $conditions[] = '(event_name LIKE ? OR location LIKE ?)';
                $params[] = $searchPattern;
                $params[] = $searchPattern;
            }
        }

        // Location filter
        if ($query->hasLocationFilter()) {
            $conditions[] = 'location LIKE ?';
            $params[] = '%' . $query->location . '%';
        }

        // Geographic search (radius-based) - optimized with spatial indexing considerations
        if ($query->hasGeographicSearch()) {
            // First filter by bounding box for performance (uses spatial index)
            $latRange = $query->radius / 111.0; // Approximate degrees per km for latitude
            $lngRange = $query->radius / (111.0 * cos(deg2rad($query->latitude ?? 0.0))); // Longitude adjustment

            $conditions[] = 'latitude BETWEEN ? AND ?';
            $params[] = $query->latitude - $latRange;
            $params[] = $query->latitude + $latRange;

            $conditions[] = 'longitude BETWEEN ? AND ?';
            $params[] = $query->longitude - $lngRange;
            $params[] = $query->longitude + $lngRange;

            // Then apply precise distance calculation
            $conditions[] = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?';
            $params[] = $query->latitude;
            $params[] = $query->longitude;
            $params[] = $query->latitude;
            $params[] = $query->radius;
        }

        // Date range filter
        if ($query->hasDateFilter()) {
            if ($query->dateFrom) {
                $conditions[] = 'DATE(created_at) >= ?';
                $params[] = $query->dateFrom;
            }

            if ($query->dateTo) {
                $conditions[] = 'DATE(created_at) <= ?';
                $params[] = $query->dateTo;
            }
        }

        // Add WHERE clause if we have conditions
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Add ordering and pagination for non-count queries
        if (!$countOnly) {
            $sortColumn = $this->mapSortColumn($query->sortBy);
            $sortDirection = $query->getSortDirection();

            // Special ordering for geographic search
            if ($query->hasGeographicSearch() && $query->sortBy === 'id') {
                $sql .= ' ORDER BY distance ASC, id ASC';
            } else {
                $sql .= " ORDER BY {$sortColumn} {$sortDirection}";
            }

            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = $query->getLimit();
            $params[] = $query->getOffset();
        }

        return [$sql, $params];
    }
}
