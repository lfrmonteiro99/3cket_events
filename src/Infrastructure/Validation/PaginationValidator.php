<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

class PaginationValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $rules = []): ValidationResult
    {
        $errors = [];

        if (!is_array($value)) {
            return ValidationResult::failure(['Pagination data must be an array']);
        }

        // Validate page parameter
        if (isset($value['page'])) {
            $pageResult = $this->validatePage($value['page']);

            if (!$pageResult->isValid()) {
                $errors = array_merge($errors, $pageResult->getErrors());
            }
        }

        // Validate page size parameter
        if (isset($value['page_size'])) {
            $pageSizeResult = $this->validatePageSize($value['page_size']);

            if (!$pageSizeResult->isValid()) {
                $errors = array_merge($errors, $pageSizeResult->getErrors());
            }
        }

        // Validate sort by parameter
        if (isset($value['sort_by'])) {
            $sortByResult = $this->validateSortBy($value['sort_by']);

            if (!$sortByResult->isValid()) {
                $errors = array_merge($errors, $sortByResult->getErrors());
            }
        }

        // Validate sort direction parameter
        if (isset($value['sort_direction'])) {
            $sortDirectionResult = $this->validateSortDirection($value['sort_direction']);

            if (!$sortDirectionResult->isValid()) {
                $errors = array_merge($errors, $sortDirectionResult->getErrors());
            }
        }

        return empty($errors) ? ValidationResult::success() : ValidationResult::failure($errors);
    }

    private function validatePage(mixed $page): ValidationResult
    {
        if (!is_numeric($page) || (int) $page < 1) {
            return ValidationResult::failure(['Page must be a positive integer']);
        }

        return ValidationResult::success();
    }

    private function validatePageSize(mixed $pageSize): ValidationResult
    {
        if (!is_numeric($pageSize) || (int) $pageSize < 1 || (int) $pageSize > 100) {
            return ValidationResult::failure(['Page size must be between 1 and 100']);
        }

        return ValidationResult::success();
    }

    private function validateSortBy(mixed $sortBy): ValidationResult
    {
        $validSortFields = ['id', 'event_name', 'location', 'created_at'];

        if (!is_string($sortBy) || !in_array($sortBy, $validSortFields, true)) {
            return ValidationResult::failure(['Invalid sort field. Valid fields: ' . implode(', ', $validSortFields)]);
        }

        return ValidationResult::success();
    }

    private function validateSortDirection(mixed $sortDirection): ValidationResult
    {
        $validDirections = ['ASC', 'DESC'];

        if (!is_string($sortDirection) || !in_array(strtoupper($sortDirection), $validDirections, true)) {
            return ValidationResult::failure(['Sort direction must be ASC or DESC']);
        }

        return ValidationResult::success();
    }
}
