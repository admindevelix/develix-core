<?php

declare(strict_types=1);

namespace Core\Config;

use Core\Core\ServiceProvider;

final class ConfigServiceProvider extends ServiceProvider
{
    /**
     * @param array<string, mixed> $items
     */
    public function __construct(
        \Core\Contracts\ContainerInterface $container,
        private array $items = [],
    ) {
        parent::__construct($container);
    }

    public function register(): void
    {
        $items = $this->items;

        $this->container->singleton(
            Config::class,
            static fn (): Config => new Config($items)
        );
    }
}