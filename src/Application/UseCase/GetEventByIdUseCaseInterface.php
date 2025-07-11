<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EventDto;
use App\Application\Query\GetEventByIdQuery;

interface GetEventByIdUseCaseInterface
{
    public function execute(GetEventByIdQuery $query): ?EventDto;
}
