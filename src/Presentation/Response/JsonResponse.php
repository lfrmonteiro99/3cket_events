<?php

declare(strict_types=1);

namespace App\Presentation\Response;

final class JsonResponse
{
    /** @var array<string, mixed> */
    private array $data;

    private HttpStatus $statusCode;

    /** @var array<string, string> */
    private array $headers;

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $headers
     */
    public function __construct(array $data, HttpStatus $statusCode = HttpStatus::OK, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    public function send(): void
    {
        http_response_code($this->statusCode->value);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function success(array $data, HttpStatus $statusCode = HttpStatus::OK): self
    {
        return new self($data, $statusCode);
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function error(string $message, HttpStatus $statusCode = HttpStatus::BAD_REQUEST, array $details = []): self
    {
        $data = ['error' => $message];

        if (!empty($details)) {
            $data['details'] = $details;
        }

        return new self($data, $statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return self::error($message, HttpStatus::NOT_FOUND);
    }
}
