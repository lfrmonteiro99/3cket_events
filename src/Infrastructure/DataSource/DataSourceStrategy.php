<?php

declare(strict_types=1);

namespace App\Infrastructure\DataSource;

enum DataSourceStrategy: string
{
    case DATABASE_FIRST = 'database_first';
    case CSV_FIRST = 'csv_first';
    case DATABASE_ONLY = 'database_only';
    case CSV_ONLY = 'csv_only';
    case AUTO = 'auto';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::AUTO;
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::DATABASE_FIRST => 'Database First (with CSV fallback)',
            self::CSV_FIRST => 'CSV First (with Database fallback)',
            self::DATABASE_ONLY => 'Database Only',
            self::CSV_ONLY => 'CSV Only',
            self::AUTO => 'Auto-detect Best Available',
        };
    }
} 