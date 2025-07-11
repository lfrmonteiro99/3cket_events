<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

class HtmlResponseFormatter implements ResponseFormatterInterface
{
    public function formatSuccess(array $data, HttpStatus $status = HttpStatus::OK): string
    {
        if (empty($data)) {
            return $this->createHtmlPage('Success', '<p>No data available.</p>');
        }

        // Handle both single events and arrays of events
        if (isset($data[0]) && is_array($data[0])) {
            // Array of events - create a table
            return $this->createEventTable($data);
        }

        // Single event or other data structure
        return $this->createEventDetail($data);

    }

    public function formatError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): string
    {
        $errorContent = "<div class='error'>";
        $errorContent .= "<h2>Error {$status->value}</h2>";
        $errorContent .= '<p><strong>Message:</strong> ' . htmlspecialchars($message) . '</p>';

        if (!empty($details)) {
            $errorContent .= '<h3>Details:</h3>';
            $errorContent .= '<pre>' . htmlspecialchars(json_encode($details, JSON_PRETTY_PRINT) ?: 'JSON encoding failed') . '</pre>';
        }

        $errorContent .= '</div>';

        return $this->createHtmlPage('Error', $errorContent);
    }

    public function getContentType(): string
    {
        return 'text/html';
    }

    public function getHeaders(): array
    {
        return [
            'Cache-Control' => 'no-cache, must-revalidate',
        ];
    }

    /**
     * Create a complete HTML page.
     */
    private function createHtmlPage(string $title, string $content): string
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Event API - {$title}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                    .container { max-width: 1200px; margin: 0 auto; }
                    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
                    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .event-detail { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
                    .error { background: #ffe6e6; border: 1px solid #ff0000; padding: 20px; border-radius: 5px; }
                    h1 { color: #333; }
                    h2 { color: #666; }
                    pre { background: #f4f4f4; padding: 10px; border-radius: 3px; overflow-x: auto; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Event Management API</h1>
                    {$content}
                </div>
            </body>
            </html>
            HTML;
    }

    /**
     * Create HTML table for event list.
     *
     * @param array<array<string, mixed>> $events
     */
    private function createEventTable(array $events): string
    {
        $content = '<h2>Events</h2>';
        $content .= '<table>';
        $content .= '<thead><tr>';
        $content .= '<th>ID</th><th>Event Name</th><th>Location</th><th>Latitude</th><th>Longitude</th><th>Created</th><th>Updated</th>';
        $content .= '</tr></thead>';
        $content .= '<tbody>';

        foreach ($events as $event) {
            $content .= '<tr>';
            $content .= '<td>' . htmlspecialchars((string) ($event['id'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['event_name'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['location'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['latitude'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['longitude'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['created_at'] ?? '')) . '</td>';
            $content .= '<td>' . htmlspecialchars((string) ($event['updated_at'] ?? '')) . '</td>';
            $content .= '</tr>';
        }

        $content .= '</tbody></table>';
        $content .= '<p><strong>Total events:</strong> ' . count($events) . '</p>';

        return $this->createHtmlPage('Events List', $content);
    }

    /**
     * Create HTML for single event detail.
     *
     * @param array<string, mixed> $event
     */
    private function createEventDetail(array $event): string
    {
        $content = '<h2>Event Details</h2>';
        $content .= "<div class='event-detail'>";

        foreach ($event as $key => $value) {
            $displayKey = ucwords(str_replace(['_', '-'], ' ', $key));
            $content .= "<p><strong>{$displayKey}:</strong> " . htmlspecialchars((string) $value) . '</p>';
        }

        $content .= '</div>';

        return $this->createHtmlPage('Event Detail', $content);
    }
}
