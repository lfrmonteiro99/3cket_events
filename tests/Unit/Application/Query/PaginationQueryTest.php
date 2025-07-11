<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Query;

use App\Application\Query\PaginationQuery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PaginationQueryTest extends TestCase
{
    public function testValidPaginationQueryIsCreated(): void
    {
        $query = new PaginationQuery(2, 20, 'event_name', 'DESC');

        $this->assertEquals(2, $query->page);
        $this->assertEquals(20, $query->pageSize);
        $this->assertEquals('event_name', $query->sortBy);
        $this->assertEquals('DESC', $query->sortDirection);
    }

    public function testDefaultValuesAreUsed(): void
    {
        $query = new PaginationQuery();

        $this->assertEquals(1, $query->page);
        $this->assertEquals(10, $query->pageSize);
        $this->assertEquals('id', $query->sortBy);
        $this->assertEquals('ASC', $query->sortDirection);
    }

    public function testInvalidPageThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be greater than 0');

        new PaginationQuery(0);
    }

    public function testInvalidPageSizeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page size must be between 1 and 100');

        new PaginationQuery(1, 0);
    }

    public function testPageSizeTooLargeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page size must be between 1 and 100');

        new PaginationQuery(1, 101);
    }

    public function testInvalidSortDirectionThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sort direction must be ASC or DESC');

        new PaginationQuery(1, 10, 'id', 'INVALID');
    }

    public function testInvalidSortByThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sort field');

        new PaginationQuery(1, 10, 'invalid_field');
    }

    public function testGetOffsetCalculation(): void
    {
        $query = new PaginationQuery(3, 10);

        $this->assertEquals(20, $query->getOffset());
    }

    public function testGetLimitReturnsPageSize(): void
    {
        $query = new PaginationQuery(1, 25);

        $this->assertEquals(25, $query->getLimit());
    }

    public function testGetSortDirectionNormalizesCase(): void
    {
        $query = new PaginationQuery(1, 10, 'id', 'desc');

        $this->assertEquals('DESC', $query->getSortDirection());
    }

    public function testGetCacheKeyIsGenerated(): void
    {
        $query = new PaginationQuery(2, 15, 'event_name', 'ASC');

        $expectedKey = 'pagination_event_name_ASC_2_15';
        $this->assertEquals($expectedKey, $query->getCacheKey());
    }

    public function testValidSortFields(): void
    {
        $validFields = ['id', 'event_name', 'location', 'created_at'];

        foreach ($validFields as $field) {
            $query = new PaginationQuery(1, 10, $field);
            $this->assertEquals($field, $query->sortBy);
        }
    }

    public function testSortDirectionCaseInsensitive(): void
    {
        $query1 = new PaginationQuery(1, 10, 'id', 'asc');
        $query2 = new PaginationQuery(1, 10, 'id', 'ASC');

        $this->assertEquals('ASC', $query1->getSortDirection());
        $this->assertEquals('ASC', $query2->getSortDirection());
    }
}
