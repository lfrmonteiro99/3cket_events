<?php

declare(strict_types=1);

namespace App\Router;

use App\Exception\RouteNotFoundException;
use App\Service\Container;

class Router
{
    /** @var array<Route> */
    private array $routes = [];

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function addRoute(HttpMethod $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($method, $path, $controller, $action);
    }

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute(HttpMethod::GET, $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute(HttpMethod::POST, $path, $controller, $action);
    }

    public function resolve(string $method, string $path): Route
    {
        $httpMethod = HttpMethod::fromString($method);

        foreach ($this->routes as $route) {
            if ($route->matches($httpMethod, $path)) {
                return $route;
            }
        }

        throw new RouteNotFoundException("Route not found: {$method} {$path}");
    }

    public function dispatch(string $method, string $path): void
    {
        $route = $this->resolve($method, $path);

        $controllerClass = $route->getController();
        $action = $route->getAction();

        try {
            $controller = $this->container->get($controllerClass);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Controller not found in container: {$controllerClass}");
        }

        if (!method_exists($controller, $action)) {
            throw new \InvalidArgumentException("Action method not found: {$action}");
        }

        // Extract route parameters
        $parameters = $route->extractParameters($path);

        // Call controller method with parameters
        if (empty($parameters)) {
            $controller->$action();
        } else {
            $controller->$action($parameters);
        }
    }
}
