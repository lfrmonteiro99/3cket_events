<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

enum ResponseFormatStrategy: string
{
    public static function fromString(string $value): self
    {
        return self::tryFrom(strtolower($value)) ?? self::JSON;
    }

    public static function fromAcceptHeader(string $acceptHeader): self
    {
        // Parse Accept header to determine preferred format
        $acceptHeader = strtolower($acceptHeader);

        if (str_contains($acceptHeader, 'application/xml') || str_contains($acceptHeader, 'text/xml')) {
            return self::XML;
        }

        if (str_contains($acceptHeader, 'text/csv') || str_contains($acceptHeader, 'application/csv')) {
            return self::CSV;
        }

        if (str_contains($acceptHeader, 'text/html')) {
            return self::HTML;
        }

        // Default to JSON
        return self::JSON;
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::JSON => 'JSON',
            self::XML => 'XML',
            self::CSV => 'CSV',
            self::HTML => 'HTML',
        };
    }

    public function getFileExtension(): string
    {
        return match ($this) {
            self::JSON => 'json',
            self::XML => 'xml',
            self::CSV => 'csv',
            self::HTML => 'html',
        };
    }

    public function createFormatter(): ResponseFormatterInterface
    {
        return match ($this) {
            self::JSON => new JsonResponseFormatter(),
            self::XML => new XmlResponseFormatter(),
            self::CSV => new CsvResponseFormatter(),
            self::HTML => new HtmlResponseFormatter(),
        };
    }
    case JSON = 'json';
    case XML = 'xml';
    case CSV = 'csv';
    case HTML = 'html';
}
