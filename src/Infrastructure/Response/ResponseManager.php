<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

class ResponseManager
{
    private ResponseFormatterInterface $formatter;
    private ResponseFormatStrategy $strategy;

    public function __construct(?ResponseFormatStrategy $strategy = null)
    {
        $this->strategy = $strategy ?? $this->detectFormat();
        $this->formatter = $this->strategy->createFormatter();
    }

    /**
     * Send successful response
     *
     * @param array<string, mixed> $data
     * @param HttpStatus $status
     */
    public function sendSuccess(array $data, HttpStatus $status = HttpStatus::OK): void
    {
        $content = $this->formatter->formatSuccess($data, $status);
        $this->sendResponse($content, $status);
    }

    /**
     * Send error response
     *
     * @param string $message
     * @param HttpStatus $status
     * @param array<string, mixed> $details
     */
    public function sendError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): void
    {
        $content = $this->formatter->formatError($message, $status, $details);
        $this->sendResponse($content, $status);
    }

    /**
     * Send 404 Not Found response
     */
    public function sendNotFound(string $message = 'Resource not found'): void
    {
        $this->sendError($message, HttpStatus::NOT_FOUND);
    }

    /**
     * Get the current format strategy
     */
    public function getStrategy(): ResponseFormatStrategy
    {
        return $this->strategy;
    }

    /**
     * Get the current formatter
     */
    public function getFormatter(): ResponseFormatterInterface
    {
        return $this->formatter;
    }

    /**
     * Detect response format from request
     */
    private function detectFormat(): ResponseFormatStrategy
    {
        // Priority 1: Query parameter 'format'
        if (isset($_GET['format'])) {
            return ResponseFormatStrategy::fromString($_GET['format']);
        }

        // Priority 2: Accept header
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? 'application/json';
        return ResponseFormatStrategy::fromAcceptHeader($acceptHeader);
    }

    /**
     * Send the formatted response
     */
    private function sendResponse(string $content, HttpStatus $status): void
    {
        // Set HTTP response code
        http_response_code($status->value);

        // Set content type header
        header('Content-Type: ' . $this->formatter->getContentType());

        // Set additional headers from formatter
        foreach ($this->formatter->getHeaders() as $name => $value) {
            header("{$name}: {$value}");
        }

        // Output the content
        echo $content;
    }



    /**
     * Create ResponseManager from environment/request
     */
    public static function createFromRequest(): self
    {
        return new self();
    }

    /**
     * Create ResponseManager with specific format
     */
    public static function createWithFormat(ResponseFormatStrategy $strategy): self
    {
        return new self($strategy);
    }
} 