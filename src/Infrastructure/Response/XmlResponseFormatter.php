<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use App\Presentation\Response\HttpStatus;

class XmlResponseFormatter implements ResponseFormatterInterface
{
    public function formatSuccess(array $data, HttpStatus $status = HttpStatus::OK): string
    {
        return $this->arrayToXml($data, 'response');
    }

    public function formatError(string $message, HttpStatus $status = HttpStatus::BAD_REQUEST, array $details = []): string
    {
        $errorData = ['error' => $message];

        if (!empty($details)) {
            $errorData['details'] = $details;
        }

        return $this->arrayToXml($errorData, 'error_response');
    }

    public function getContentType(): string
    {
        return 'application/xml';
    }

    public function getHeaders(): array
    {
        return [
            'Cache-Control' => 'no-cache, must-revalidate',
        ];
    }

    /**
     * Convert array to XML string
     *
     * @param array<string, mixed> $data
     * @param string $rootElement
     * @return string
     */
    private function arrayToXml(array $data, string $rootElement = 'root'): string
    {
        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$rootElement}></{$rootElement}>");
        
        $this->addArrayToXml($data, $xml);
        
        return $xml->asXML() ?: '';
    }

    /**
     * Recursively add array elements to XML
     *
     * @param array<string, mixed> $data
     * @param \SimpleXMLElement $xml
     */
    private function addArrayToXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item'; // Handle numeric keys
                }
                $subNode = $xml->addChild($key);
                $this->addArrayToXml($value, $subNode);
            } else {
                if (is_numeric($key)) {
                    $key = 'item'; // Handle numeric keys
                }
                $xml->addChild($key, htmlspecialchars((string) $value));
            }
        }
    }
} 