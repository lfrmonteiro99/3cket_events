<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Application\Bootstrap;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    private Bootstrap $bootstrap;

    /**
     * Test /events endpoint with various pagination parameters.
     */
    public function testEventsEndpointWithValidParameters(): void
    {
        $_GET = [
            'page' => '1',
            'page_size' => '5',
            'sort_by' => 'event_name',
            'sort_direction' => 'DESC',
        ];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('data', $response['body']);
        $this->assertArrayHasKey('pagination', $response['body']);
        $this->assertCount(5, $response['body']['data']);
    }

    public function testEventsEndpointWithDefaultParameters(): void
    {
        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('data', $response['body']);
        $this->assertArrayHasKey('pagination', $response['body']);
        $this->assertEquals(1, $response['body']['pagination']['current_page']);
        $this->assertEquals(10, $response['body']['pagination']['page_size']);
    }

    public function testEventsEndpointWithInvalidPage(): void
    {
        $_GET = ['page' => '0'];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Invalid pagination parameters', $response['body']['error']);
    }

    public function testEventsEndpointWithInvalidPageSize(): void
    {
        $_GET = ['page_size' => '1000'];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Invalid pagination parameters', $response['body']['error']);
    }

    public function testEventsEndpointWithInvalidSortDirection(): void
    {
        $_GET = ['sort_direction' => 'INVALID'];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Invalid pagination parameters', $response['body']['error']);
    }

    /**
     * Test /events/{id} endpoint.
     */
    public function testShowEventWithValidId(): void
    {
        $response = $this->makeRequest('GET', '/events/1');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('id', $response['body']);
        $this->assertArrayHasKey('event_name', $response['body']);
        $this->assertArrayHasKey('location', $response['body']);
    }

    public function testShowEventWithInvalidId(): void
    {
        $response = $this->makeRequest('GET', '/events/invalid');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Event ID must be a numeric value', $response['body']['error']);
    }

    public function testShowEventWithNonExistentId(): void
    {
        $response = $this->makeRequest('GET', '/events/99999');

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Event not found', $response['body']['error']);
    }

    public function testShowEventWithoutId(): void
    {
        $response = $this->makeRequest('GET', '/events/');

        $this->assertEquals(404, $response['status']);
        $this->assertStringContainsString('Route not found', $response['body']['error']);
    }

    /**
     * Test /search endpoint with various search parameters.
     */
    public function testSearchEndpointWithTextSearch(): void
    {
        $_GET = [
            'search' => 'concert',
            'page' => '1',
            'page_size' => '10',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('data', $response['body']);
        $this->assertArrayHasKey('pagination', $response['body']);
        $this->assertArrayHasKey('search_info', $response['body']);
        $this->assertEquals('concert', $response['body']['search_info']['search_term']);
    }

    public function testSearchEndpointWithLocationFilter(): void
    {
        $_GET = [
            'location' => 'Lisboa',
            'page' => '1',
            'page_size' => '5',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Lisboa', $response['body']['search_info']['location_filter']);
        $this->assertTrue($response['body']['search_info']['filters_applied']);
    }

    public function testSearchEndpointWithGeographicSearch(): void
    {
        $_GET = [
            'lat' => '38.7223',
            'lng' => '-9.1393',
            'radius' => '10',
            'page' => '1',
            'page_size' => '5',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertTrue($response['body']['search_info']['geographic_search']);
        $this->assertTrue($response['body']['search_info']['filters_applied']);
    }

    public function testSearchEndpointWithDateFilter(): void
    {
        $_GET = [
            'date_from' => '2024-01-01',
            'date_to' => '2024-12-31',
            'page' => '1',
            'page_size' => '10',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertTrue($response['body']['search_info']['date_filter']);
        $this->assertTrue($response['body']['search_info']['filters_applied']);
    }

    public function testSearchEndpointWithAllFilters(): void
    {
        $_GET = [
            'search' => 'festival',
            'location' => 'Porto',
            'lat' => '41.1579',
            'lng' => '-8.6291',
            'radius' => '5',
            'date_from' => '2024-06-01',
            'date_to' => '2024-08-31',
            'page' => '2',
            'page_size' => '5',
            'sort_by' => 'event_name',
            'sort_direction' => 'ASC',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('festival', $response['body']['search_info']['search_term']);
        $this->assertEquals('Porto', $response['body']['search_info']['location_filter']);
        $this->assertTrue($response['body']['search_info']['geographic_search']);
        $this->assertTrue($response['body']['search_info']['date_filter']);
        $this->assertTrue($response['body']['search_info']['filters_applied']);
    }

    public function testSearchEndpointWithInvalidPage(): void
    {
        $_GET = ['page' => '0'];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Page must be greater than 0', $response['body']['error']);
    }

    public function testSearchEndpointWithInvalidCoordinates(): void
    {
        $_GET = [
            'lat' => 'invalid',
            'lng' => 'invalid',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Invalid coordinates', $response['body']['error']);
    }

    public function testSearchEndpointWithInvalidDateRange(): void
    {
        $_GET = [
            'date_from' => '2024-12-31',
            'date_to' => '2024-01-01',
        ];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(400, $response['status']);
    }

    /**
     * Test /search/nearby endpoint.
     */
    public function testNearbyEndpointWithValidCoordinates(): void
    {
        $_GET = [
            'lat' => '38.7223',
            'lng' => '-9.1393',
            'radius' => '10',
            'limit' => '5',
        ];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('data', $response['body']);
        $this->assertArrayHasKey('search_center', $response['body']);
        $this->assertArrayHasKey('results_count', $response['body']);
        $this->assertEquals(38.7223, $response['body']['search_center']['latitude']);
        $this->assertEquals(-9.1393, $response['body']['search_center']['longitude']);
        $this->assertEquals(10.0, $response['body']['search_center']['radius_km']);
    }

    public function testNearbyEndpointWithDefaultParameters(): void
    {
        $_GET = [
            'lat' => '38.7223',
            'lng' => '-9.1393',
        ];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(0, $response['body']['results_count']);
    }

    public function testNearbyEndpointWithoutLatitude(): void
    {
        $_GET = ['lng' => '-9.1393'];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Latitude and longitude are required', $response['body']['error']);
    }

    public function testNearbyEndpointWithoutLongitude(): void
    {
        $_GET = ['lat' => '38.7223'];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Latitude and longitude are required', $response['body']['error']);
    }

    public function testNearbyEndpointWithInvalidCoordinates(): void
    {
        $_GET = [
            'lat' => 'invalid',
            'lng' => 'invalid',
        ];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(400, $response['status']);
        $this->assertStringContainsString('Invalid coordinates', $response['body']['error']);
    }

    public function testNearbyEndpointWithInvalidRadius(): void
    {
        $_GET = [
            'lat' => '38.7223',
            'lng' => '-9.1393',
            'radius' => 'invalid',
        ];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(500, $response['status']);
    }

    /**
     * Test /search/suggestions endpoint.
     */
    public function testSuggestionsEndpointWithQuery(): void
    {
        $_GET = [
            'q' => 'concert',
            'limit' => '5',
        ];

        $response = $this->makeRequest('GET', '/search/suggestions');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('suggestions', $response['body']);
        $this->assertArrayHasKey('query', $response['body']);
        $this->assertArrayHasKey('count', $response['body']);
        $this->assertEquals('concert', $response['body']['query']);
    }

    public function testSuggestionsEndpointWithoutQuery(): void
    {
        $response = $this->makeRequest('GET', '/search/suggestions');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('suggestions', $response['body']);
        $this->assertArrayHasKey('query', $response['body']);
        $this->assertArrayHasKey('count', $response['body']);
        $this->assertEquals('', $response['body']['query']);
    }

    public function testSuggestionsEndpointWithDefaultLimit(): void
    {
        $response = $this->makeRequest('GET', '/search/suggestions');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('suggestions', $response['body']);
        $this->assertArrayHasKey('query', $response['body']);
        $this->assertArrayHasKey('count', $response['body']);
        $this->assertEquals(10, $response['body']['count']);
    }

    public function testSuggestionsEndpointWithCustomLimit(): void
    {
        $_GET = [
            'q' => 'music',
            'limit' => '3',
        ];

        $response = $this->makeRequest('GET', '/search/suggestions');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(0, $response['body']['count']);
    }

    /**
     * Test /debug endpoint.
     */
    public function testDebugEndpoint(): void
    {
        $response = $this->makeRequest('GET', '/debug');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('process_id', $response['body']);
        $this->assertArrayHasKey('event_count', $response['body']);
        $this->assertArrayHasKey('timestamp', $response['body']);
        $this->assertArrayHasKey('pooling_enabled', $response['body']);
        $this->assertArrayHasKey('caching_enabled', $response['body']);
        $this->assertArrayHasKey('cache_stats', $response['body']);
        $this->assertArrayHasKey('message', $response['body']);
        $this->assertTrue($response['body']['pooling_enabled']);
    }

    /**
     * Test /cache endpoint with various actions.
     */
    public function testCacheEndpointWithStatsAction(): void
    {
        $_GET = ['action' => 'stats'];

        $response = $this->makeRequest('GET', '/cache');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('stats', $response['body']['action']);
        $this->assertEquals('Cache Statistics', $response['body']['action_name']);
        $this->assertArrayHasKey('cache_stats', $response['body']);
        $this->assertArrayHasKey('available_actions', $response['body']);
    }

    public function testCacheEndpointWithClearAction(): void
    {
        $_GET = ['action' => 'clear'];

        $response = $this->makeRequest('GET', '/cache');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('clear', $response['body']['action']);
        $this->assertEquals('Clear Cache', $response['body']['action_name']);
        $this->assertTrue($response['body']['success']);
        $this->assertEquals('Cache cleared successfully', $response['body']['message']);
    }

    public function testCacheEndpointWithDefaultAction(): void
    {
        $response = $this->makeRequest('GET', '/cache');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('stats', $response['body']['action']);
        $this->assertEquals('Cache Statistics', $response['body']['action_name']);
    }

    public function testCacheEndpointWithInvalidAction(): void
    {
        $_GET = ['action' => 'invalid'];

        $response = $this->makeRequest('GET', '/cache');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('stats', $response['body']['action']); // Falls back to stats
    }

    /**
     * Test /cache/analytics endpoint.
     */
    public function testCacheAnalyticsEndpoint(): void
    {
        $response = $this->makeRequest('GET', '/cache/analytics');

        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('performance', $response['body']);
        $this->assertArrayHasKey('total_requests', $response['body']['performance']);
        $this->assertArrayHasKey('total_hits', $response['body']['performance']);
        $this->assertArrayHasKey('total_misses', $response['body']['performance']);
        $this->assertArrayHasKey('hit_rate', $response['body']['performance']);
    }

    /**
     * Test POST endpoints.
     */
    public function testCacheWarmUpEndpoint(): void
    {
        $response = $this->makeRequest('POST', '/cache/warm-up');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Cache warm-up completed successfully', $response['body']['message']);
        $this->assertArrayHasKey('timestamp', $response['body']);
    }

    public function testInvalidateCacheEndpoint(): void
    {
        $response = $this->makeRequest('POST', '/cache/invalidate');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Cache invalidated successfully', $response['body']['message']);
        $this->assertArrayHasKey('timestamp', $response['body']);
    }

    public function testInvalidateEventCacheEndpointWithValidId(): void
    {
        $response = $this->makeRequest('POST', '/cache/invalidate/event/1');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Cache invalidated for event 1', $response['body']['message']);
        $this->assertArrayHasKey('timestamp', $response['body']);
    }

    public function testInvalidateEventCacheEndpointWithoutId(): void
    {
        $response = $this->makeRequest('POST', '/cache/invalidate/event/');

        $this->assertEquals(404, $response['status']);
        $this->assertStringContainsString('Route not found', $response['body']['error']);
    }

    public function testInvalidateSearchCacheEndpoint(): void
    {
        $response = $this->makeRequest('POST', '/cache/invalidate/search');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Search cache invalidated successfully', $response['body']['message']);
        $this->assertArrayHasKey('timestamp', $response['body']);
    }

    /**
     * Test error handling for non-existent endpoints.
     */
    public function testNonExistentEndpoint(): void
    {
        $response = $this->makeRequest('GET', '/non-existent');

        $this->assertEquals(404, $response['status']);
        $this->assertStringContainsString('Route not found', $response['body']['error']);
    }

    public function testNonExistentMethod(): void
    {
        $response = $this->makeRequest('POST', '/events');

        $this->assertEquals(404, $response['status']);
        $this->assertStringContainsString('Route not found', $response['body']['error']);
    }

    /**
     * Test edge cases and boundary conditions.
     */
    public function testEventsEndpointWithMaximumPageSize(): void
    {
        $_GET = ['page_size' => '100'];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(100, $response['body']['pagination']['page_size']);
    }

    public function testEventsEndpointWithLargePageNumber(): void
    {
        $_GET = ['page' => '999'];

        $response = $this->makeRequest('GET', '/events');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(999, $response['body']['pagination']['current_page']);
    }

    public function testSearchEndpointWithEmptySearchTerm(): void
    {
        $_GET = ['search' => ''];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertFalse($response['body']['search_info']['filters_applied']);
    }

    public function testSearchEndpointWithSpecialCharacters(): void
    {
        $_GET = ['search' => 'festival & concert'];

        $response = $this->makeRequest('GET', '/search');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('festival & concert', $response['body']['search_info']['search_term']);
    }

    public function testNearbyEndpointWithBoundaryCoordinates(): void
    {
        $_GET = [
            'lat' => '90.0',
            'lng' => '180.0',
            'radius' => '0.1',
        ];

        $response = $this->makeRequest('GET', '/search/nearby');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(90.0, $response['body']['search_center']['latitude']);
        $this->assertEquals(180.0, $response['body']['search_center']['longitude']);
        $this->assertEquals(0.1, $response['body']['search_center']['radius_km']);
    }

    protected function setUp(): void
    {
        // Suppress deprecation warnings for testing
        error_reporting(E_ALL & ~E_DEPRECATED);

        // Set up environment variables for testing
        $_ENV['DATA_SOURCE_STRATEGY'] = 'csv';
        $_ENV['CACHE_STRATEGY'] = 'memory';
        $_ENV['LOGGING_STRATEGY'] = 'null';

        $this->bootstrap = new Bootstrap();

        // Clear any previous test data
        unset($_GET, $_POST, $_SERVER);
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        unset($_GET, $_POST, $_SERVER);
    }

    /**
     * Helper method to make HTTP requests.
     *
     * @return array<string, mixed>
     */
    private function makeRequest(string $method, string $path): array
    {
        // Set up the request environment
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        // Capture output
        ob_start();

        try {
            $this->bootstrap->handleRequest();
            $output = ob_get_clean();

            // Parse the response
            $responseCode = http_response_code();
            $responseBody = null;

            if (is_string($output)) {
                $responseBody = json_decode($output, true);
            }

            if (!is_array($responseBody) || json_last_error() !== JSON_ERROR_NONE) {
                $responseBody = ['raw_output' => $output];
            }

            // @var array<string, mixed>
            return [
                'status' => $responseCode,
                'body' => $responseBody,
            ];
        } catch (\Exception $e) {
            ob_end_clean();

            // @var array<string, mixed>
            return [
                'status' => 500,
                'body' => ['error' => $e->getMessage()],
            ];
        }
    }
}
