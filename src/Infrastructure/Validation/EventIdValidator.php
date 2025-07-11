<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

class EventIdValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $rules = []): ValidationResult
    {
        // Check if value is numeric
        if (!is_numeric($value)) {
            return ValidationResult::failure(['Event ID must be a numeric value']);
        }

        // Check if value is an integer (not a float)
        if (!ctype_digit((string) $value)) {
            return ValidationResult::failure(['Event ID must be a positive integer']);
        }

        $id = (int) $value;

        // Check if ID is positive
        if ($id <= 0) {
            return ValidationResult::failure(['Event ID must be a positive integer']);
        }

        // Check maximum reasonable ID value (optional rule)
        $maxId = $rules['max_id'] ?? PHP_INT_MAX;

        if ($id > $maxId) {
            return ValidationResult::failure(["Event ID cannot be greater than {$maxId}"]);
        }

        return ValidationResult::success();
    }
}
