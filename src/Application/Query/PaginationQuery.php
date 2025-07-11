<?php

declare(strict_types=1);

namespace App\Application\Query;

readonly class PaginationQuery
{
    public function __construct(
        public int $page = 1,
        public int $pageSize = 10,
        public string $sortBy = 'id',
        public string $sortDirection = 'ASC'
    ) {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }

        if ($pageSize < 1 || $pageSize > 100) {
            throw new \InvalidArgumentException('Page size must be between 1 and 100');
        }

        if (!in_array(strtoupper($sortDirection), ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException('Sort direction must be ASC or DESC');
        }

        if (!in_array($sortBy, ['id', 'event_name', 'location', 'created_at'], true)) {
            throw new \InvalidArgumentException('Invalid sort field');
        }
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }

    public function getLimit(): int
    {
        return $this->pageSize;
    }

    public function getSortDirection(): string
    {
        return strtoupper($this->sortDirection);
    }

    public function getCacheKey(): string
    {
        return sprintf(
            'pagination_%s_%s_%d_%d',
            $this->sortBy,
            $this->getSortDirection(),
            $this->page,
            $this->pageSize
        );
    }
}
