<?php

declare(strict_types=1);

namespace App\Service;

interface ServiceProvider
{
    public function register(Container $container): void;
}
