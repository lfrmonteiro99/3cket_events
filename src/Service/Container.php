<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Providers\ApplicationServiceProvider;
use App\Service\Providers\InfrastructureServiceProvider;
use App\Service\Providers\PresentationServiceProvider;

class Container
{
    /** @var array<string, callable> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /** @var array<ServiceProvider> */
    private array $providers = [];

    public function __construct()
    {
        $this->registerProviders();
        $this->configure();
    }

    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new \InvalidArgumentException("Service not bound: {$abstract}");
        }

        $instance = $this->bindings[$abstract]($this);
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function configure(): void
    {
        foreach ($this->providers as $provider) {
            $provider->register($this);
        }
    }

    private function registerProviders(): void
    {
        $this->providers = [
            new InfrastructureServiceProvider(),
            new ApplicationServiceProvider(),
            new PresentationServiceProvider(),
        ];
    }
}
