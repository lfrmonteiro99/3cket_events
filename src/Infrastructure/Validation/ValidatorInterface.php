<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

interface ValidatorInterface
{
    /**
     * Validate input data
     *
     * @param mixed $value
     * @param array<string, mixed> $rules
     * @return ValidationResult
     */
    public function validate(mixed $value, array $rules): ValidationResult;
}

final class ValidationResult
{
    public function __construct(
        private readonly bool $isValid,
        private readonly array $errors = []
    ) {
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(array $errors): self
    {
        return new self(false, $errors);
    }
} 