<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\DTO\PaginatedResponse;
use App\Application\Query\GetPaginatedEventsQuery;

interface GetPaginatedEventsUseCaseInterface
{
    /**
     * @return PaginatedResponse<EventDto>
     */
    public function execute(GetPaginatedEventsQuery $query): PaginatedResponse;
}
