<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Application\Query\PaginationQuery;
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

    public function findById(EventId $id): ?Event;

    public function count(): int;
}
