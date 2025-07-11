<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

enum CacheStrategy: string
{
    public function isNone(): bool
    {
        return $this === self::NONE || $this === self::NULL;
    }

    public function isMemory(): bool
    {
        return $this === self::MEMORY || $this === self::IN_MEMORY;
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::AUTO;
    }
    case REDIS = 'redis';
    case MEMORY = 'memory';
    case IN_MEMORY = 'inmemory';
    case NONE = 'none';
    case NULL = 'null';
    case AUTO = 'auto';
}
