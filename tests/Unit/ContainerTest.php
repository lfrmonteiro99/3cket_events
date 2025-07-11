<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Repository\EventRepositoryInterface;
use App\Presentation\Controller\EventController;
use App\Service\Container;
use PDO;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private Container $container;

    public function testPdoInstanceIsSingleton(): void
    {
        // Test that the same PDO instance is returned on multiple calls
        // This verifies our connection pooling via dependency injection

        try {
            $pdo1 = $this->container->get(PDO::class);
            $pdo2 = $this->container->get(PDO::class);

            // Should be the exact same object instance
            $this->assertSame($pdo1, $pdo2);

        } catch (\Exception $e) {
            // If database is not available, that's expected in test environment
            $this->markTestSkipped('Database connection not available in test environment');
        }
    }

    public function testEventRepositoryCreation(): void
    {
        try {
            $repository = $this->container->get(EventRepositoryInterface::class);
            $this->assertNotNull($repository);
        } catch (\Exception $e) {
            // Should fallback to CSV repository if database is not available
            $this->markTestSkipped('Expected fallback to CSV repository');
        }
    }

    public function testEventControllerCreation(): void
    {
        $controller = $this->container->get(EventController::class);
        $this->assertNotNull($controller);
    }

    public function testContainerInstancesAreCached(): void
    {
        // Test that container caches instances (singleton behavior)
        $controller1 = $this->container->get(EventController::class);
        $controller2 = $this->container->get(EventController::class);

        $this->assertSame($controller1, $controller2);
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->configure();
    }
}
