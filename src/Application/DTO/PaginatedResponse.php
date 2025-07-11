<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * @template T
 */
readonly class PaginatedResponse
{
    /**
     * @param array<T> $data
     * @param int      $totalItems
     * @param int      $currentPage
     * @param int      $pageSize
     * @param int      $totalPages
     */
    public function __construct(
        public array $data,
        public int $totalItems,
        public int $currentPage,
        public int $pageSize,
        public int $totalPages
    ) {
    }

    /**
     * @param array<T> $data
     * @param int      $totalItems
     * @param int      $currentPage
     * @param int      $pageSize
     *
     * @return PaginatedResponse<T>
     */
    public static function create(
        array $data,
        int $totalItems,
        int $currentPage,
        int $pageSize
    ): self {
        $totalPages = (int) ceil($totalItems / $pageSize);

        return new self(
            $data,
            $totalItems,
            $currentPage,
            $pageSize,
            $totalPages
        );
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function getStartItem(): int
    {
        return $this->isEmpty() ? 0 : (($this->currentPage - 1) * $this->pageSize) + 1;
    }

    public function getEndItem(): int
    {
        return $this->isEmpty() ? 0 : min($this->totalItems, $this->currentPage * $this->pageSize);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'pagination' => [
                'current_page' => $this->currentPage,
                'page_size' => $this->pageSize,
                'total_items' => $this->totalItems,
                'total_pages' => $this->totalPages,
                'has_next_page' => $this->hasNextPage(),
                'has_previous_page' => $this->hasPreviousPage(),
                'next_page' => $this->getNextPage(),
                'previous_page' => $this->getPreviousPage(),
                'start_item' => $this->getStartItem(),
                'end_item' => $this->getEndItem(),
            ],
        ];
    }
}
