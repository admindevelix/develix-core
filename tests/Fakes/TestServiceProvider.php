<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Core\Core\ServiceProvider;

final class TestServiceProvider extends ServiceProvider
{
    public static int $registrations = 0;

    public static int $boots = 0;

    public function register(): void
    {
        self::$registrations++;
    }

    public function boot(): void
    {
        self::$boots++;
    }

    public static function reset(): void
    {
        self::$registrations = 0;
        self::$boots = 0;
    }
}