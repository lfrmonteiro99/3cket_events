<?php

declare(strict_types=1);

namespace App\Router;

use App\Presentation\Controller\EventController;

class RouteProvider
{
    public static function registerRoutes(Router $router): void
    {
        // Event management routes
        $router->get('/events', EventController::class, 'index');           // All events (paginated)
        $router->get('/events/{id}', EventController::class, 'show');       // Specific event by ID

        // Search routes
        $router->get('/search', EventController::class, 'search');          // Advanced search
        $router->get('/search/nearby', EventController::class, 'nearby');   // Geographic search
        $router->get('/search/suggestions', EventController::class, 'suggestions'); // Search suggestions

        // System management routes
        $router->get('/debug', EventController::class, 'debug');            // Debug info
        $router->get('/cache', EventController::class, 'cache');            // Cache management
        $router->get('/cache/analytics', EventController::class, 'cacheAnalytics'); // Cache analytics
        $router->post('/cache/warm-up', EventController::class, 'cacheWarmUp'); // Cache warm-up
        $router->post('/cache/invalidate', EventController::class, 'invalidateCache'); // Invalidate all cache
        $router->post('/cache/invalidate/event/{id}', EventController::class, 'invalidateEventCache'); // Invalidate event cache
        $router->post('/cache/invalidate/search', EventController::class, 'invalidateSearchCache'); // Invalidate search cache
    }
}
