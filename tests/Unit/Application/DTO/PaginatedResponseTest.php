<?php

declare(strict_types=1);

namespace Tests\Unit\Application\DTO;

use App\Application\DTO\PaginatedResponse;
use PHPUnit\Framework\TestCase;

class PaginatedResponseTest extends TestCase
{
    public function testCreatePaginatedResponse(): void
    {
        $data = ['item1', 'item2', 'item3'];
        
        $response = PaginatedResponse::create($data, 100, 2, 10);
        
        $this->assertEquals($data, $response->data);
        $this->assertEquals(100, $response->totalItems);
        $this->assertEquals(2, $response->currentPage);
        $this->assertEquals(10, $response->pageSize);
        $this->assertEquals(10, $response->totalPages);
    }

    public function testCreateCalculatesTotalPages(): void
    {
        $response = PaginatedResponse::create(['item1'], 25, 1, 10);
        
        $this->assertEquals(3, $response->totalPages);
    }

    public function testCreateWithExactDivision(): void
    {
        $response = PaginatedResponse::create(['item1'], 20, 1, 10);
        
        $this->assertEquals(2, $response->totalPages);
    }

    public function testHasNextPageTrue(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 2, 10);
        
        $this->assertTrue($response->hasNextPage());
    }

    public function testHasNextPageFalse(): void
    {
        $response = PaginatedResponse::create(['item1'], 10, 1, 10);
        
        $this->assertFalse($response->hasNextPage());
    }

    public function testHasPreviousPageTrue(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 2, 10);
        
        $this->assertTrue($response->hasPreviousPage());
    }

    public function testHasPreviousPageFalse(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 1, 10);
        
        $this->assertFalse($response->hasPreviousPage());
    }

    public function testGetNextPageReturnsCorrectPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 2, 10);
        
        $this->assertEquals(3, $response->getNextPage());
    }

    public function testGetNextPageReturnsNullOnLastPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 10, 1, 10);
        
        $this->assertNull($response->getNextPage());
    }

    public function testGetPreviousPageReturnsCorrectPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 3, 10);
        
        $this->assertEquals(2, $response->getPreviousPage());
    }

    public function testGetPreviousPageReturnsNullOnFirstPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 1, 10);
        
        $this->assertNull($response->getPreviousPage());
    }

    public function testIsEmptyTrue(): void
    {
        $response = PaginatedResponse::create([], 0, 1, 10);
        
        $this->assertTrue($response->isEmpty());
    }

    public function testIsEmptyFalse(): void
    {
        $response = PaginatedResponse::create(['item1'], 1, 1, 10);
        
        $this->assertFalse($response->isEmpty());
    }

    public function testGetStartItemWithData(): void
    {
        $response = PaginatedResponse::create(['item1'], 100, 3, 10);
        
        $this->assertEquals(21, $response->getStartItem());
    }

    public function testGetStartItemWithEmptyData(): void
    {
        $response = PaginatedResponse::create([], 0, 1, 10);
        
        $this->assertEquals(0, $response->getStartItem());
    }

    public function testGetEndItemWithData(): void
    {
        $response = PaginatedResponse::create(['item1', 'item2'], 100, 3, 10);
        
        $this->assertEquals(30, $response->getEndItem());
    }

    public function testGetEndItemWithPartialPage(): void
    {
        $response = PaginatedResponse::create(['item1', 'item2'], 22, 3, 10);
        
        $this->assertEquals(22, $response->getEndItem());
    }

    public function testGetEndItemWithEmptyData(): void
    {
        $response = PaginatedResponse::create([], 0, 1, 10);
        
        $this->assertEquals(0, $response->getEndItem());
    }

    public function testToArrayStructure(): void
    {
        $data = ['item1', 'item2'];
        $response = PaginatedResponse::create($data, 25, 2, 10);
        
        $array = $response->toArray();
        
        $this->assertEquals($data, $array['data']);
        $this->assertEquals(2, $array['pagination']['current_page']);
        $this->assertEquals(10, $array['pagination']['page_size']);
        $this->assertEquals(25, $array['pagination']['total_items']);
        $this->assertEquals(3, $array['pagination']['total_pages']);
        $this->assertTrue($array['pagination']['has_next_page']);
        $this->assertTrue($array['pagination']['has_previous_page']);
        $this->assertEquals(3, $array['pagination']['next_page']);
        $this->assertEquals(1, $array['pagination']['previous_page']);
        $this->assertEquals(11, $array['pagination']['start_item']);
        $this->assertEquals(20, $array['pagination']['end_item']);
    }

    public function testToArrayWithNoNextPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 10, 1, 10);
        
        $array = $response->toArray();
        
        $this->assertFalse($array['pagination']['has_next_page']);
        $this->assertNull($array['pagination']['next_page']);
    }

    public function testToArrayWithNoPreviousPage(): void
    {
        $response = PaginatedResponse::create(['item1'], 10, 1, 10);
        
        $array = $response->toArray();
        
        $this->assertFalse($array['pagination']['has_previous_page']);
        $this->assertNull($array['pagination']['previous_page']);
    }
} 