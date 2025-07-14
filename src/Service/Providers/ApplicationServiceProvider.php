<?php

declare(strict_types=1);

namespace App\Service\Providers;

use App\Application\Service\EventService;
use App\Application\Service\EventServiceInterface;
use App\Application\Service\SearchService;
use App\Application\Service\SearchServiceInterface;
use App\Application\UseCase\GetAllEventsUseCase;
use App\Application\UseCase\GetEventByIdUseCase;
use App\Application\UseCase\GetPaginatedEventsUseCase;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Validation\EventIdValidator;
use App\Infrastructure\Validation\PaginationValidator;
use App\Service\Container;
use App\Service\ServiceProvider;

class ApplicationServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $this->registerServices($container);
        $this->registerValidators($container);
        $this->registerUseCases($container);
    }

    private function registerServices(Container $container): void
    {
        $container->bind(EventServiceInterface::class, function (Container $container) {
            return new EventService($container->get(EventRepositoryInterface::class));
        });

        $container->bind(SearchServiceInterface::class, function (Container $container) {
            return new SearchService(
                $container->get(EventRepositoryInterface::class),
                $container->get(LoggerInterface::class)
            );
        });
    }

    private function registerValidators(Container $container): void
    {
        $container->bind(PaginationValidator::class, fn () => new PaginationValidator());
        $container->bind(EventIdValidator::class, fn () => new EventIdValidator());
    }

    private function registerUseCases(Container $container): void
    {
        // Keep use cases for potential future use or migration scenarios
        $container->bind(GetAllEventsUseCase::class, function (Container $container) {
            return new GetAllEventsUseCase($container->get(EventRepositoryInterface::class));
        });

        $container->bind(GetEventByIdUseCase::class, function (Container $container) {
            return new GetEventByIdUseCase($container->get(EventRepositoryInterface::class));
        });

        $container->bind(GetPaginatedEventsUseCase::class, function (Container $container) {
            return new GetPaginatedEventsUseCase($container->get(EventRepositoryInterface::class));
        });
    }
}
