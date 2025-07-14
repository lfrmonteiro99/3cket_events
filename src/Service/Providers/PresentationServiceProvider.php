<?php

declare(strict_types=1);

namespace App\Service\Providers;

use App\Application\Service\EventServiceInterface;
use App\Application\Service\SearchServiceInterface;
use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;
use App\Infrastructure\Response\ResponseManager;
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
                $container->get(SearchServiceInterface::class),
                $container->get(EventRepositoryInterface::class),
                $container->get(\App\Infrastructure\Validation\ValidatorBag::class),
                $container->get(ResponseManager::class),
                $container->get(LoggerInterface::class),
                $container->get(\App\Infrastructure\Cache\CacheManager::class)
            );
        });
    }
}
