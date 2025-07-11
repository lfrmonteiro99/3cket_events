<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

class JsonResponseFormatter implements ResponseFormatterInterface
{
    public function formatSuccess(array $data, HttpStatus $status = HttpStatus::OK): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function formatError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): string
    {
        $errorData = ['error' => $message];

        if (!empty($details)) {
            $errorData['details'] = $details;
        }

        return json_encode($errorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getContentType(): string
    {
        return 'application/json';
    }

    public function getHeaders(): array
    {
        return [
            'Cache-Control' => 'no-cache, must-revalidate',
        ];
    }
} 