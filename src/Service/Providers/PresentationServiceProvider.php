<?php

declare(strict_types=1);

namespace App\Service\Providers;

use App\Application\Service\EventServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Response\ResponseManager;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Presentation\Controller\EventController;
use App\Service\Container;
use App\Service\ServiceProvider;

class PresentationServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $this->registerControllers($container);
    }

    private function registerControllers(Container $container): void
    {
        $container->bind(EventController::class, function (Container $container) {
            return new EventController(
                $container->get(EventServiceInterface::class),
                $container->get(EventRepositoryInterface::class),
                $container->get(PaginationValidator::class),
                $container->get(EventIdValidator::class),
                ResponseManager::createFromRequest(), // Fresh instance per request
                $container->get(LoggerInterface::class)
            );
        });
    }
}
