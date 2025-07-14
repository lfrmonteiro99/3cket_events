<?php

declare(strict_types=1);

namespace App\Application\Query;

readonly class SearchQuery extends PaginationQuery
{
    public function __construct(
        public ?string $search = null,
        public ?string $location = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $radius = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        int $page = 1,
        int $pageSize = 10,
        string $sortBy = 'id',
        string $sortDirection = 'ASC'
    ) {
        parent::__construct($page, $pageSize, $sortBy, $sortDirection);

        $this->validateSearchParameters();
    }

    public function hasSearch(): bool
    {
        return !empty($this->search);
    }

    public function hasLocationFilter(): bool
    {
        return !empty($this->location);
    }

    public function hasGeographicSearch(): bool
    {
        return $this->latitude !== null
            && $this->longitude !== null
            && $this->radius !== null;
    }

    public function hasDateFilter(): bool
    {
        return !empty($this->dateFrom) || !empty($this->dateTo);
    }

    public function hasAnyFilter(): bool
    {
        return $this->hasSearch()
            || $this->hasLocationFilter()
            || $this->hasGeographicSearch()
            || $this->hasDateFilter();
    }

    public function getCacheKey(): string
    {
        $filters = [
            'search' => $this->search ?? '',
            'location' => $this->location ?? '',
            'geo' => $this->hasGeographicSearch() ? "{$this->latitude},{$this->longitude},{$this->radius}" : '',
            'date' => ($this->dateFrom ?? '') . '-' . ($this->dateTo ?? ''),
        ];

        $filterKey = md5(serialize($filters));

        return sprintf(
            'search_%s_%s_%s_%d_%d_%s',
            $this->sortBy,
            $this->getSortDirection(),
            $filterKey,
            $this->page,
            $this->pageSize,
            date('Y-m-d-H') // Cache for 1 hour
        );
    }

    private function validateSearchParameters(): void
    {
        if ($this->hasGeographicSearch()) {
            if ($this->latitude < -90 || $this->latitude > 90) {
                throw new \InvalidArgumentException('Latitude must be between -90 and 90');
            }

            if ($this->longitude < -180 || $this->longitude > 180) {
                throw new \InvalidArgumentException('Longitude must be between -180 and 180');
            }

            if ($this->radius <= 0 || $this->radius > 1000) {
                throw new \InvalidArgumentException('Radius must be between 0 and 1000 km');
            }
        }

        if ($this->dateFrom && !$this->isValidDate($this->dateFrom)) {
            throw new \InvalidArgumentException('Invalid dateFrom format. Use YYYY-MM-DD');
        }

        if ($this->dateTo && !$this->isValidDate($this->dateTo)) {
            throw new \InvalidArgumentException('Invalid dateTo format. Use YYYY-MM-DD');
        }

        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            throw new \InvalidArgumentException('dateFrom cannot be after dateTo');
        }
    }

    private function isValidDate(string $date): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
            && strtotime($date) !== false;
    }
}
