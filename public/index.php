<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Bootstrap;

// Bootstrap and run the application
(new Bootstrap())->run();
