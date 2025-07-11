<?php

declare(strict_types=1);

namespace App\Application\Query;

final class GetEventByIdQuery
{
    public function __construct(
        public readonly int $id
    ) {
    }
}
