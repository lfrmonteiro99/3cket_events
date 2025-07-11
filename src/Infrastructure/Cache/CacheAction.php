<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

enum CacheAction: string
{
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::STATS;
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::CLEAR => 'Clear Cache',
            self::STATS => 'Cache Statistics',
        };
    }
    case CLEAR = 'clear';
    case STATS = 'stats';
}
