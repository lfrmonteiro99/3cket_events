<?php

declare(strict_types=1);

namespace App\Application;

use App\Exception\RouteNotFoundException;
use App\Infrastructure\Logging\LoggerInterface;
use App\Presentation\Response\HttpStatus;
use App\Presentation\Response\JsonResponse;
use App\Router\RouteProvider;
use App\Router\Router;
use App\Service\Container;

class Bootstrap
{
    private Container $container;
    private Router $router;
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->setupErrorReporting();
        $this->container = new Container();
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->router = new Router($this->container);
        RouteProvider::registerRoutes($this->router);

        $this->logger->logApplicationEvent('Application bootstrap completed');
    }

    /**
     * Handle a request for testing purposes.
     */
    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        try {
            // Log the incoming request
            $this->logger->logRequest($method, $path, [
                'timestamp' => date('Y-m-d H:i:s'),
                'headers' => $this->getRequestHeaders(),
            ]);

            $this->router->dispatch($method, $path);

            // Log successful response
            $this->logger->logResponse(200, $method, $path, [
                'duration' => 0,
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
            ]);

        } catch (RouteNotFoundException $e) {
            // Log 404 response
            $this->logger->logResponse(404, $method, $path, [
                'duration' => 0,
                'error' => $e->getMessage(),
            ]);

            $this->logger->logSecurityEvent('Route not found', [
                'method' => $method,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            JsonResponse::notFound('Route not found')->send();
        } catch (\Exception $e) {
            // Log 500 response
            $this->logger->logResponse(500, $method, $path, [
                'duration' => 0,
                'error' => $e->getMessage(),
            ]);

            $this->logger->error('Unhandled exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            JsonResponse::error('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR)->send();
        }
    }

    public function run(): void
    {
        $startTime = microtime(true);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        try {

            // Log the incoming request
            $this->logger->logRequest($method, $path, [
                'timestamp' => date('Y-m-d H:i:s'),
                'headers' => $this->getRequestHeaders(),
            ]);

            $this->router->dispatch($method, $path);

            $duration = microtime(true) - $startTime;

            // Log successful response
            $this->logger->logResponse(200, $method, $path, [
                'duration' => $duration,
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
            ]);

            // Log performance metrics
            $this->logger->logPerformance('Request completed', $duration, [
                'method' => $method,
                'path' => $path,
            ]);

        } catch (RouteNotFoundException $e) {
            $duration = microtime(true) - $startTime;

            // Log 404 response
            $this->logger->logResponse(404, $method, $path, [
                'duration' => $duration,
                'error' => $e->getMessage(),
            ]);

            $this->logger->logSecurityEvent('Route not found', [
                'method' => $method,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            JsonResponse::notFound('Route not found')->send();
        } catch (\Exception $e) {
            $duration = microtime(true) - $startTime;

            // Log 500 response
            $this->logger->logResponse(500, $method, $path, [
                'duration' => $duration,
                'error' => $e->getMessage(),
            ]);

            $this->logger->error('Unhandled exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            JsonResponse::error('Internal server error', HttpStatus::INTERNAL_SERVER_ERROR)->send();
        }
    }

    /**
     * @return array<string, string>
     */
    private function getRequestHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    private function setupErrorReporting(): void
    {
        // Enable error reporting for development
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }
}
