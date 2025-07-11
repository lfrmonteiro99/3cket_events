<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

class CsvResponseFormatter implements ResponseFormatterInterface
{
    public function formatSuccess(array $data, HttpStatus $status = HttpStatus::OK): string
    {
        if (empty($data)) {
            return '';
        }

        // Handle both single events and arrays of events
        if (isset($data[0]) && is_array($data[0])) {
            // Array of events
            return $this->arrayToCsv($data);
        }

        // Single event or other data structure
        return $this->arrayToCsv([$data]);

    }

    public function formatError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): string
    {
        $errorData = [
            'error' => $message,
            'status_code' => $status->value,
            'details' => !empty($details) ? json_encode($details) : '',
        ];

        return $this->arrayToCsv([$errorData]);
    }

    public function getContentType(): string
    {
        return 'text/csv';
    }

    public function getHeaders(): array
    {
        return [
            'Content-Disposition' => 'attachment; filename="events.csv"',
        ];
    }

    /**
     * Convert array to CSV string.
     *
     * @param array<array<string, mixed>> $data
     *
     * @return string
     */
    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        if ($output === false) {
            throw new \RuntimeException('Failed to create temporary file for CSV output');
        }

        // Write headers from first row
        $headers = array_keys($data[0]);
        fputcsv($output, $headers);

        // Write data rows
        foreach ($data as $row) {
            $values = [];

            foreach ($headers as $header) {
                $values[] = $row[$header] ?? '';
            }
            fputcsv($output, $values);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }
}
