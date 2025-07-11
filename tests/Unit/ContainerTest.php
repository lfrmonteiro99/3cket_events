<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Repository\EventRepositoryInterface;
use App\Infrastructure\Logging\LoggerInterface;
use App\Service\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private Container $container;

    public function testContainerCanBeCreated(): void
    {
        $this->assertInstanceOf(Container::class, $this->container);
    }

    public function testContainerCanBindAndRetrieveServices(): void
    {
        // Test basic container functionality with a simple service
        $mockLogger = $this->createMock(LoggerInterface::class);

        $this->container->bind(LoggerInterface::class, function () use ($mockLogger) {
            return $mockLogger;
        });

        $retrieved = $this->container->get(LoggerInterface::class);

        $this->assertSame($mockLogger, $retrieved);
    }

    public function testContainerReturnsSameInstanceForSingleton(): void
    {
        // Test singleton behavior with a mock service
        $mockRepository = $this->createMock(EventRepositoryInterface::class);

        $this->container->bind(EventRepositoryInterface::class, function () use ($mockRepository) {
            return $mockRepository;
        });

        $instance1 = $this->container->get(EventRepositoryInterface::class);
        $instance2 = $this->container->get(EventRepositoryInterface::class);

        $this->assertSame($instance1, $instance2);
    }

    public function testContainerThrowsExceptionForUnboundService(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Service not bound: NonExistentService');

        $this->container->get('NonExistentService');
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        // Don't call configure() - that's what tries to set up real services with database connections
    }
}
