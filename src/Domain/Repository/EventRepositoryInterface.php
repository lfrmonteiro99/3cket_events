<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Application\Query\PaginationQuery;
use App\Application\Query\SearchQuery;
use App\Domain\Entity\Event;
use App\Domain\ValueObject\EventId;

interface EventRepositoryInterface
{
    /**
     * @return Event[]
     */
    public function findAll(): array;

    /**
     * @return Event[]
     */
    public function findPaginated(PaginationQuery $query): array;

    /**
     * Search events with advanced filtering.
     *
     * @return Event[]
     */
    public function search(SearchQuery $query): array;

    /**
     * Count events matching search criteria.
     */
    public function countSearch(SearchQuery $query): int;

    public function findById(EventId $id): ?Event;

    public function count(): int;
}
