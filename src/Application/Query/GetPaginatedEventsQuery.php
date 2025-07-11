<?php

declare(strict_types=1);

namespace App\Application\Query;

readonly class GetPaginatedEventsQuery
{
    public function __construct(
        public PaginationQuery $pagination
    ) {
    }
}
