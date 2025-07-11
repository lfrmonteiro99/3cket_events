<?php

declare(strict_types=1);

namespace App\Router;

enum HttpMethod: string
{
    public static function fromString(string $value): self
    {
        $normalized = strtoupper($value);

        return self::tryFrom($normalized) ?? throw new \InvalidArgumentException("Invalid HTTP method: {$value}");
    }

    public function isSafe(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::OPTIONS => true,
            default => false,
        };
    }

    public function isIdempotent(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::OPTIONS, self::PUT, self::DELETE => true,
            default => false,
        };
    }
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';
}
