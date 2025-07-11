<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Command\CreateEventCommand;
use App\Application\DTO\EventDto;

interface CreateEventUseCaseInterface
{
    public function execute(CreateEventCommand $command): EventDto;
}
