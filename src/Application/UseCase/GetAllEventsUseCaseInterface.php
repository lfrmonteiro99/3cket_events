<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\Query\GetAllEventsQuery;

interface GetAllEventsUseCaseInterface
{
    /**
     * @return array<EventDto>
     */
    public function execute(GetAllEventsQuery $query): array;
}
