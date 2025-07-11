<?php

declare(strict_types=1);

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use App\Exception\RouteNotFoundException;
use App\Presentation\Controller\EventController;
use App\Presentation\Response\HttpStatus;
use App\Presentation\Response\JsonResponse;
use App\Router\Router;
use App\Service\Container;

try {
    // Set up dependency injection container
    $container = new Container();
    $container->configure();

    // Create router
    $router = new Router($container);

    // Define routes
    $router->get('/address', EventController::class, 'show');                    // Legacy route (ID=1)
    $router->get('/events', EventController::class, 'index');                   // All events
    $router->get('/events/paginated', EventController::class, 'paginated');     // Paginated events
    $router->get('/events/{id}', EventController::class, 'show');               // Specific event by ID
    $router->get('/debug', EventController::class, 'debug');                    // Debug info
    $router->get('/cache', EventController::class, 'cache');                    // Cache management

    // Get request information
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    // Dispatch request (controllers handle their own response formatting)
    $router->dispatch($method, $path);

} catch (RouteNotFoundException $e) {
    JsonResponse::notFound('Route not found')->send();
} catch (Exception $e) {
    JsonResponse::error('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR)->send();

    // Log error for debugging (in production, use proper logging)
    error_log($e->getMessage());
}
