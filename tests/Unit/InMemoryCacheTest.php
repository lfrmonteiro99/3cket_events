<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Cache\InMemoryCache;
use PHPUnit\Framework\TestCase;

class InMemoryCacheTest extends TestCase
{
    private InMemoryCache $cache;

    public function testSetAndGet(): void
    {
        $this->assertTrue($this->cache->set('test_key', 'test_value'));
        $this->assertEquals('test_value', $this->cache->get('test_key'));
    }

    public function testGetNonexistentKey(): void
    {
        $this->assertNull($this->cache->get('nonexistent'));
    }

    public function testExists(): void
    {
        $this->cache->set('existing_key', 'value');

        $this->assertTrue($this->cache->exists('existing_key'));
        $this->assertFalse($this->cache->exists('nonexistent_key'));
    }

    public function testDelete(): void
    {
        $this->cache->set('delete_me', 'value');
        $this->assertTrue($this->cache->exists('delete_me'));

        $this->assertTrue($this->cache->delete('delete_me'));
        $this->assertFalse($this->cache->exists('delete_me'));
    }

    public function testClear(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $this->assertTrue($this->cache->clear());

        $this->assertNull($this->cache->get('key1'));
        $this->assertNull($this->cache->get('key2'));
    }

    public function testTtlExpiration(): void
    {
        // Set with 1 second TTL
        $this->cache->set('ttl_key', 'value', 1);
        $this->assertEquals('value', $this->cache->get('ttl_key'));

        // Wait for expiration
        sleep(2);

        // Should be expired now
        $this->assertNull($this->cache->get('ttl_key'));
        $this->assertFalse($this->cache->exists('ttl_key'));
    }

    public function testZeroTtlNeverExpires(): void
    {
        $this->cache->set('permanent_key', 'value', 0);

        // Simulate time passing (we can't actually wait)
        // But the key should still exist
        $this->assertEquals('value', $this->cache->get('permanent_key'));
        $this->assertTrue($this->cache->exists('permanent_key'));
    }

    public function testMultipleOperations(): void
    {
        $values = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $this->assertTrue($this->cache->setMultiple($values));

        $retrieved = $this->cache->getMultiple(['key1', 'key2', 'key3', 'nonexistent']);

        $this->assertEquals('value1', $retrieved['key1']);
        $this->assertEquals('value2', $retrieved['key2']);
        $this->assertEquals('value3', $retrieved['key3']);
        $this->assertNull($retrieved['nonexistent']);
    }

    public function testStats(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $stats = $this->cache->getStats();

        $this->assertEquals(2, $stats['total_items']);
    }

    protected function setUp(): void
    {
        $this->cache = new InMemoryCache();
    }
}
