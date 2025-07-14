<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

class ValidatorBag
{
    private EventIdValidator $eventIdValidator;
    private PaginationValidator $paginationValidator;

    public function __construct(
        EventIdValidator $eventIdValidator,
        PaginationValidator $paginationValidator
    ) {
        $this->eventIdValidator = $eventIdValidator;
        $this->paginationValidator = $paginationValidator;
    }

    public function eventId(): EventIdValidator
    {
        return $this->eventIdValidator;
    }

    public function pagination(): PaginationValidator
    {
        return $this->paginationValidator;
    }
}
