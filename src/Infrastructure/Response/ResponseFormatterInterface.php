<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

interface ResponseFormatterInterface
{
    /**
     * Format successful response
     *
     * @param array<string, mixed> $data
     * @param HttpStatus $status
     * @return string
     */
    public function formatSuccess(array $data, HttpStatus $status = HttpStatus::OK): string;

    /**
     * Format error response
     *
     * @param string $message
     * @param HttpStatus $status
     * @param array<string, mixed> $details
     * @return string
     */
    public function formatError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): string;

    /**
     * Get content type for this format
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Get additional HTTP headers for this format
     *
     * @return array<string, string>
     */
    public function getHeaders(): array;
} 