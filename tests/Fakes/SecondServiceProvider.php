<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Core\Core\ServiceProvider;

final class SecondServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        FirstServiceProvider::$events[] = 'second.register';
    }

    public function boot(): void
    {
        FirstServiceProvider::$events[] = 'second.boot';
    }
}