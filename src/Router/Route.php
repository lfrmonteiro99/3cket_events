<?php

declare(strict_types=1);

namespace App\Router;

class Route
{
    private HttpMethod $method;
    private string $path;
    private string $controller;
    private string $action;

    public function __construct(HttpMethod $method, string $path, string $controller, string $action)
    {
        $this->method = $method;
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function matches(HttpMethod $method, string $path): bool
    {
        if ($this->method !== $method) {
            return false;
        }

        return $this->matchesPath($path);
    }

    public function matchesPath(string $path): bool
    {
        $pattern = $this->convertToRegex($this->path);
        return preg_match($pattern, $path) === 1;
    }

    /**
     * @return array<string, string>
     */
    public function extractParameters(string $path): array
    {
        $pattern = $this->convertToRegex($this->path);
        $parameters = [];
        
        if (preg_match($pattern, $path, $matches)) {
            // Extract parameter names from the route path
            if (preg_match_all('/\{(\w+)\}/', $this->path, $paramMatches)) {
                $paramNames = $paramMatches[1];
                
                // Skip the first match (full string) and map parameters
                for ($i = 1; $i < count($matches); $i++) {
                    if (isset($paramNames[$i - 1])) {
                        $parameters[$paramNames[$i - 1]] = $matches[$i];
                    }
                }
            }
        }
        
        return $parameters;
    }

    private function convertToRegex(string $path): string
    {
        // Simple approach: manually build the regex pattern
        $pattern = $path;
        
        // Escape forward slashes for regex delimiters
        $pattern = str_replace('/', '\/', $pattern);
        
        // Replace {parameter} with capture groups
        $pattern = preg_replace('/\{(\w+)\}/', '([^\/]+)', $pattern);
        
        // Add anchors
        return '/^' . $pattern . '$/';
    }
}
