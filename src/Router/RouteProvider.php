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

        // System management routes
        $router->get('/debug', EventController::class, 'debug');            // Debug info
        $router->get('/cache', EventController::class, 'cache');            // Cache management
    }
}
