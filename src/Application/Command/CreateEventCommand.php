<?php

declare(strict_types=1);

namespace App\Application\Command;

final class CreateEventCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $location,
        public readonly float $latitude,
        public readonly float $longitude
    ) {
    }
}
