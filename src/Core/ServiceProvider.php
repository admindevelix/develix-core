<?php

declare(strict_types=1);

namespace Core\Core;

use Core\Contracts\ContainerInterface;
use Core\Contracts\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    public function __construct(
        protected ContainerInterface $container
    ) {
    }

    abstract public function register(): void;

    public function boot(): void
    {
    }
}