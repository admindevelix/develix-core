<?php

declare(strict_types=1);

namespace Core\Core;

use Core\Contracts\ContainerInterface;
use Core\Contracts\ServiceProviderInterface;

final class Application
{
    /**
     * @var list<ServiceProviderInterface>
     */
    private array $providers = [];
    private bool $booted = false;

    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param class-string<ServiceProviderInterface> $providerClass
     */
    public function register(string $providerClass): void
    {
        foreach ($this->providers as $registeredProvider) {
            if ($registeredProvider::class === $providerClass) {
                return;
            }
        }

        $provider = new $providerClass($this->container);

        $provider->register();

        $this->providers[] = $provider;

        if ($this->booted) {
            $provider->boot();
        }
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->providers as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }
}